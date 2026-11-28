<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Admin\User;

use Override;
use Mage;
use Mage_Admin_Model_Role;
use Mage_Admin_Model_User;
use Mage_Core_Model_App_Area;
use OpenMage\Console\Command\AbstractCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Throwable;

/**
 * Ported from the admin:user:create command of n98-magerun (https://github.com/netz98/n98-magerun), with the same name and arguments
 *
 * Unlike n98-magerun, the user is checked by Mage_Admin_Model_User::validate() and the default role is Administrators.
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
#[AsCommand(name: 'admin:user:create', description: 'Create an admin user')]
final class CreateCommand extends AbstractCommand
{
    public const DEFAULT_ROLE = 'Administrators';

    private const REQUIRED_ARGUMENTS = ['username', 'email', 'password', 'firstname', 'lastname'];

    protected ?string $eventArea = Mage_Core_Model_App_Area::AREA_ADMINHTML;

    private ?string $passwordConfirmation = null;

    #[Override]
    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'User name')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Password, asked with hidden input when omitted')
            ->addArgument('firstname', InputArgument::OPTIONAL, 'First name')
            ->addArgument('lastname', InputArgument::OPTIONAL, 'Last name')
            ->addArgument('role', InputArgument::OPTIONAL, 'Name of an existing role', self::DEFAULT_ROLE)
            ->setHelp('Creates an active admin user, validated with the same rules as the admin user form.' . PHP_EOL
                . 'Missing arguments are asked for; the password is asked with hidden input and confirmed.' . PHP_EOL
                . 'A password given as argument stays in the shell history.');
    }

    #[Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        foreach (self::REQUIRED_ARGUMENTS as $name) {
            if ($input->getArgument($name) !== null) {
                continue;
            }

            if ($name === 'password') {
                $input->setArgument($name, $helper->ask($input, $output, $this->createHiddenQuestion('Password: ')));
                $this->passwordConfirmation = (string) $helper->ask($input, $output, $this->createHiddenQuestion('Confirm password: '));
                continue;
            }

            $input->setArgument($name, $helper->ask($input, $output, new Question(ucfirst($name) . ': ')));
        }
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $missing = array_filter(self::REQUIRED_ARGUMENTS, static fn(string $name): bool => $input->getArgument($name) === null);
        if ($missing !== []) {
            $this->writeError($output, sprintf('<error>Missing argument: %s.</error>', implode(', ', $missing)));

            return Command::FAILURE;
        }

        /** @var string $roleName */
        $roleName = $input->getArgument('role');
        $role = $this->findRole($roleName);
        if (!$role instanceof Mage_Admin_Model_Role) {
            $this->writeError($output, sprintf('<error>Role "%s" was not found.</error>', $roleName));

            return Command::FAILURE;
        }

        /** @var Mage_Admin_Model_User $user */
        $user = Mage::getModel('admin/user');
        $user->setData([
            'username' => $input->getArgument('username'),
            'email' => $input->getArgument('email'),
            'password' => $input->getArgument('password'),
            'firstname' => $input->getArgument('firstname'),
            'lastname' => $input->getArgument('lastname'),
            'is_active' => 1,
        ]);
        if ($this->passwordConfirmation !== null) {
            $user->setPasswordConfirmation($this->passwordConfirmation);
        }

        $errors = $user->validate();
        if (is_array($errors)) {
            foreach ($errors as $error) {
                $this->writeError($output, sprintf('<error>%s</error>', $error));
            }

            return Command::FAILURE;
        }

        // save() and saveRelations() open their own transactions, nested in this one by the adapter.
        $resource = $user->getResource();
        $resource->beginTransaction();
        try {
            $user->save();
            $user->setRoleIds([$role->getId()])
                ->setRoleUserId($user->getUserId())
                ->saveRelations();
            $resource->commit();
        } catch (Throwable $throwable) {
            $resource->rollBack();

            throw $throwable;
        }

        $output->writeln(sprintf(
            '<info>User <comment>%s</comment> created with role <comment>%s</comment></info>',
            $user->getUsername(),
            $role->getRoleName(),
        ));

        return Command::SUCCESS;
    }

    /**
     * Searches group roles only: the admin_role row of each user holds the user's first name as role_name.
     */
    private function findRole(string $roleName): ?Mage_Admin_Model_Role
    {
        $role = Mage::getResourceModel('admin/roles_collection')
            ->addFieldToFilter('role_name', $roleName)
            ->getFirstItem();

        return $role instanceof Mage_Admin_Model_Role && $role->getId() !== null ? $role : null;
    }

    private function createHiddenQuestion(string $label): Question
    {
        return (new Question($label))->setHidden(true);
    }
}
