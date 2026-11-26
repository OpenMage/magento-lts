<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Rector;

use InvalidArgumentException;
use OpenMage\Rector\ValueObject\ClassConstant;
use OpenMage\Rector\ValueObject\ReplaceArgumentValue;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\PhpParser\Node\Value\ValueResolver;
use Rector\Rector\AbstractRector;
use Rector\RuleDoc\CodeSample\ConfiguredCodeSample;
use Rector\RuleDoc\RuleDefinition;

/**
 * Replaces scalar argument values of method calls, e.g. magic strings with class constants
 *
 * Replacement for deprecated \Rector\Arguments\Rector\ClassMethod\ReplaceArgumentDefaultValueRector
 */
final class ReplaceArgumentValueRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var ReplaceArgumentValue[]
     */
    private array $replacements = [];

    public function __construct(
        private readonly ValueResolver $valueResolver,
        private readonly ReflectionProvider $reflectionProvider,
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace scalar argument values of method calls, e.g. magic strings with class constants', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeBlock extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        $this->_removeButton('add');
    }
}
CODE_SAMPLE,
                <<<'CODE_SAMPLE'
class SomeBlock extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        $this->_removeButton(self::BUTTON_TYPE_ADD);
    }
}
CODE_SAMPLE,
                [new ReplaceArgumentValue('Mage_Adminhtml_Block_Widget_Container', '_removeButton', 0, 'add', new ClassConstant('Mage_Adminhtml_Block_Widget_Container', 'BUTTON_TYPE_ADD'))],
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class, StaticCall::class];
    }

    /**
     * @param MethodCall|StaticCall $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($node->isFirstClassCallable()) {
            return null;
        }

        $hasChanged = false;

        foreach ($this->replacements as $replacement) {
            if (!$this->isName($node->name, $replacement->method)) {
                continue;
            }

            $caller = $node instanceof MethodCall ? $node->var : $node->class;
            if (!$this->isObjectType($caller, $replacement->getObjectType())) {
                continue;
            }

            $arg = $node->args[$replacement->position] ?? null;
            if (!$arg instanceof Arg || $arg->unpack) {
                continue;
            }

            if (!$this->isScalarValue($arg->value)) {
                continue;
            }

            if ($this->valueResolver->getValue($arg->value) !== $replacement->valueBefore) {
                continue;
            }

            $arg->value = $this->createValue($replacement->valueAfter, $node);
            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        foreach ($configuration as $replacement) {
            if (!$replacement instanceof ReplaceArgumentValue) {
                throw new InvalidArgumentException(sprintf('Expected instances of "%s"', ReplaceArgumentValue::class));
            }
        }

        $this->replacements = $configuration;
    }

    /**
     * Only literal values are compared; constants and other expressions are left untouched
     */
    private function isScalarValue(Expr $expr): bool
    {
        return $expr instanceof Scalar
            || ($expr instanceof ConstFetch && in_array($this->getName($expr), ['true', 'false', 'null'], true))
            || ($expr instanceof UnaryMinus && $expr->expr instanceof Scalar);
    }

    private function createValue(null|bool|ClassConstant|float|int|string $value, MethodCall|StaticCall $node): Expr
    {
        if (!$value instanceof ClassConstant) {
            return BuilderHelpers::normalizeValue($value);
        }

        $this->assertConstantExists($value);

        $class = $this->isInsideClassOf($node, $value->class)
            ? new Name('self')
            : new FullyQualified($value->class);

        return new ClassConstFetch($class, $value->constant);
    }

    private function assertConstantExists(ClassConstant $value): void
    {
        if (!$this->reflectionProvider->hasClass($value->class)
            || !$this->reflectionProvider->getClass($value->class)->hasConstant($value->constant)
        ) {
            throw new InvalidConfigurationException(sprintf('Constant "%s::%s" does not exist', $value->class, $value->constant));
        }
    }

    private function isInsideClassOf(Node $node, string $class): bool
    {
        $scope = $node->getAttribute(AttributeKey::SCOPE);
        if (!$scope instanceof Scope) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (!$classReflection instanceof ClassReflection || $classReflection->isAnonymous()) {
            return false;
        }

        return $classReflection->getName() === $class || $classReflection->isSubclassOf($class);
    }
}
