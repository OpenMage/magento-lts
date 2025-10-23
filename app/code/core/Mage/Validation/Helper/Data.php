<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Validation
 */

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @package    Mage_Validation
 */
class Mage_Validation_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Validation';

    private static ValidatorInterface $validator;

    public function __construct()
    {
        self::$validator = Validation::createValidator();
    }

    public static function getValidator(): ValidatorInterface
    {
        return self::$validator;
    }

    public function isValid(
        mixed $value,
        array|null|Constraint $constraints = null,
        array|null|string|GroupSequence $groups = null
    ): bool {
        $validator = self::getValidator();
        return $validator->validate($value, $constraints, $groups)->count() === 0;
    }

    /**
     * Validates a value against given constraints.
     */
    public function validate(
        mixed $value,
        array|null|Constraint $constraints = null,
        array|null|string|GroupSequence $groups = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();
        return $validator->validate($value, $constraints, $groups);
    }

    /**
     * Validates that a value is contained in a given set of choices.
     */
    public function validateChoice(
        mixed $value,
        array|string $options = [],
        ?array $choices = null,
        callable|null|string $callback = null,
        ?bool $multiple = null,
        ?bool $strict = null,
        ?int $min = null,
        ?int $max = null,
        ?string $message = null,
        ?string $multipleMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        ?bool $match = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Choice(
                options: $options,
                choices: $choices,
                callback: $callback,
                multiple: $multiple,
                strict: $strict,
                min: $min,
                max: $max,
                message: $message,
                multipleMessage: $multipleMessage,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
                groups: $groups,
                payload: $payload,
                match: $match,
            ),
        ]);
    }

    /**
     * Validates that a value is a valid date.
     */
    public function validateDate(
        mixed $value,
        ?array $options = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\NotBlank(),
            new Constraints\Date(
                options: $options,
                message: $message,
                groups: $groups,
                payload: $payload,
            ),
        ]);
    }

    public function validateDateTime(
        mixed $value,
        array|null|string $format = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\NotBlank(),
            new Constraints\DateTime(
                format: $format,
                message: $message,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates that a value is a valid email address.
     */
    public function validateEmail(
        mixed $value,
        ?array $options = null,
        ?string $message = null,
        ?string $mode = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\NotBlank(),
            new Constraints\Email(
                options: $options,
                message: $message,
                mode: $mode,
                normalizer: $normalizer,
                groups: $groups,
                payload: $payload,
            ),
        ]);
    }

    /**
     * Validates that a file meets given constraints.
     */
    public function validateFile(
        mixed $filePath,
        null|int|string $maxSize,
        null|array|string $extensions
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($filePath, [
            new Constraints\File(
                maxSize: $maxSize,
                extensions: $extensions,
            ),
        ]);
    }

    /**
     * Validates that a value is identical to the given one.
     */
    public function validateIdentical(
        mixed $value,
        mixed $compare,
        ?string $propertyPath = null,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();
        return $validator->validate($value, [
            new Constraints\IdenticalTo(
                value: $compare,
                propertyPath: $propertyPath,
                message: $message,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates the length of a given value.
     */
    public function validateLength(
        mixed $value,
        array|int|null $exactly = null,
        ?int $min = null,
        ?int $max = null,
        ?string $charset = null,
        ?callable $normalizer = null,
        ?string $countUnit = null,
        ?string $exactMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?string $charsetMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Length(
                exactly: $exactly,
                min: $min,
                max: $max,
                charset: $charset,
                normalizer: $normalizer,
                countUnit: $countUnit,
                exactMessage: $exactMessage,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
                charsetMessage: $charsetMessage,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates that a value is not empty.
     */
    public function validateNotEmpty(
        mixed $value,
        ?array $options = null,
        ?string $message = null,
        ?bool $allowNull = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();
        return $validator->validate($value, [
            new Constraints\NotBlank(
                options: $options,
                message: $message,
                allowNull: $allowNull,
                normalizer: $normalizer,
                groups: $groups,
                payload: $payload,
            ),
        ]);
    }

    public function validatePassword(mixed $value, int $minLength, string $message, string $minMessage): ConstraintViolationListInterface
    {
        $validator = self::getValidator();
        return $validator->validate($value, [
            new Constraints\Length(
                min: $minLength,
                minMessage: $minMessage,
            ),
            new Constraints\Regex(
                pattern: '/^(?=.*[a-z])(?=.*[0-9]).+$/iu',
                message: $message,
            ),
        ]);
    }

    /**
     * Validates that a value is within a given range.
     */
    public function validateRange(
        mixed $value,
        ?array $options = null,
        ?string $notInRangeMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?string $invalidMessage = null,
        ?string $invalidDateTimeMessage = null,
        mixed $min = null,
        ?string $minPropertyPath = null,
        mixed $max = null,
        ?string $maxPropertyPath = null,
        ?array $groups = null,
        mixed $payload = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Range(
                options: $options,
                notInRangeMessage: $notInRangeMessage,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
                invalidMessage: $invalidMessage,
                invalidDateTimeMessage: $invalidDateTimeMessage,
                min: $min,
                minPropertyPath: $minPropertyPath,
                max: $max,
                maxPropertyPath: $maxPropertyPath,
                groups: $groups,
                payload: $payload,
            ),
        ]);
    }

    /**
     * Validates a value against a regular expression.
     */
    public function validateRegex(
        mixed $value,
        ?string $pattern,
        ?string $message = null,
        ?string $htmlPattern = null,
        ?bool $match = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Regex(
                pattern: $pattern,
                message: $message,
                htmlPattern: $htmlPattern,
                match: $match,
                normalizer: $normalizer,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates that a value is of a given type.
     */
    public function validateType(
        mixed $value,
        ?string $type,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Type(
                type: $type,
                message: $message,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates that a value is a valid URL.
     */
    public function validateUrl(
        mixed $value,
        ?array $options = null,
        ?string $message = null,
        ?array $protocols = null,
        ?bool $relativeProtocol = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\NotBlank(),
            new Constraints\Url(
                options: $options,
                message: $message,
                protocols: $protocols,
                relativeProtocol: $relativeProtocol,
                normalizer: $normalizer,
                groups: $groups,
                payload: $payload,
            ),
        ]);
    }

    /**
     * @throws Exception
     */
    public function getContraintsByType(string $type, array $options = []): array|Constraint
    {
        return match ($type) {
            'Alnum'         => new Constraints\Type(type: 'alnum'),
            'Alpha'         => new Constraints\Type(type: 'alpha'),
            'Between'       => new Constraints\Range(min: $options['min'] ?? null, max: $options['max'] ?? null),
            'Callback'      => new Constraints\Callback($options),
            'Ccnum'         => new Constraints\Luhn(),
            'CreditCard'    => new Constraints\CardScheme($options),
            'Date'          => new Constraints\Date(),
            'Digits'        => new Constraints\Type(type: 'digit'),
            'Float'         => new Constraints\Type(type: 'float'),
            'GreaterThan'   => new Constraints\GreaterThan(),
            'Hostname'      => new Constraints\Hostname(),
            'Iban'          => new Constraints\Iban(),
            'Identical'     => new Constraints\IdenticalTo(),
            'InArray'       => new Constraints\Choice($options),
            'Int'           => new Constraints\Type(type: 'int'),
            'Ip'            => new Constraints\Ip(),
            'Isbn'          => new Constraints\Isbn(),
            'LessThan'      => new Constraints\LessThan(),
            'NoEmpty'       => new Constraints\NotBlank(),
            'Regex'         => new Constraints\Regex(pattern: $options['pattern'] ?? ''),
            'StringLength'  => new Constraints\Length(),
            default         => throw new Exception("Validator {$type} is not exist")
        };
    }
}
