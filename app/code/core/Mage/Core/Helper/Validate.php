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
class Mage_Core_Helper_Validate extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Core';

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
        null|array|Constraint $constraints = null,
        null|array|GroupSequence|string $groups = null
    ): bool {
        $validator = self::getValidator();
        return $validator->validate($value, $constraints, $groups)->count() === 0;
    }

    /**
     * Validates a value against given constraints.
     */
    public function validate(
        mixed $value,
        null|array|Constraint $constraints = null,
        null|array|GroupSequence|string $groups = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();
        return $validator->validate($value, $constraints, $groups);
    }

    /**
     * Validates that a value is contained in a given set of choices.
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function validateChoice(
        mixed $value,
        array|string $options = [],
        ?array $choices = null,
        null|callable|string $callback = null,
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
     * Validates the count of elements in a given value.
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function validateCount(
        mixed $value,
        null|array|int $exactly = null,
        ?int $min = null,
        ?int $max = null,
        ?int $divisibleBy = null,
        ?string $exactMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?string $divisibleByMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        array $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Count(
                exactly: $exactly,
                min: $min,
                max: $max,
                divisibleBy: $divisibleBy,
                exactMessage: $exactMessage,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
                divisibleByMessage: $divisibleByMessage,
                groups: $groups,
                payload: $payload,
                options: $options,
            ),
        ]);
    }

    /**
     * Validates that a value is a valid date.
     */
    public function validateDate(
        mixed   $value,
        ?array  $options = null,
        ?string $message = null,
        ?array  $groups = null,
        mixed   $payload = null,
        ?bool   $empty = true,
        ?string $emptyMessage = null,
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        $constraints = [];
        $constraints[] = new Constraints\Date(
            options: $options,
            message: $message,
            groups: $groups,
            payload: $payload,
        );

        if (!$empty) {
            $constraints[] = new Constraints\NotBlank(
                message: $emptyMessage,
            );
        }

        return $validator->validate($value, $constraints);
    }

    public function validateDateTime(
        mixed             $value,
        null|array|string $format = null,
        ?string           $message = null,
        ?array            $groups = null,
        mixed             $payload = null,
        ?bool             $empty = true,
        ?string           $emptyMessage = null,
        array             $options = []
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        $constraints = [];
        $constraints[] = new Constraints\DateTime(
            format: $format,
            message: $message,
            groups: $groups,
            payload: $payload,
            options: $options,
        );

        if (!$empty) {
            $constraints[] = new Constraints\NotBlank(
                message: $emptyMessage,
            );
        }

        return $validator->validate($value, $constraints);
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
            new Constraints\NotBlank(
                message: $message,
            ),
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
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function validateFile(
        mixed $value,
        ?array $options = null,
        null|int|string $maxSize = null,
        ?bool $binaryFormat = null,
        null|array|string $mimeTypes = null,
        ?int $filenameMaxLength = null,
        ?string $notFoundMessage = null,
        ?string $notReadableMessage = null,
        ?string $maxSizeMessage = null,
        ?string $mimeTypesMessage = null,
        ?string $disallowEmptyMessage = null,
        ?string $filenameTooLongMessage = null,
        ?string $uploadIniSizeErrorMessage = null,
        ?string $uploadFormSizeErrorMessage = null,
        ?string $uploadPartialErrorMessage = null,
        ?string $uploadNoFileErrorMessage = null,
        ?string $uploadNoTmpDirErrorMessage = null,
        ?string $uploadCantWriteErrorMessage = null,
        ?string $uploadExtensionErrorMessage = null,
        ?string $uploadErrorMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        null|array|string $extensions = null,
        ?string $extensionsMessage = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate(value: $value, constraints: [
            new Constraints\File(
                options: $options,
                maxSize: $maxSize,
                binaryFormat: $binaryFormat,
                mimeTypes: $mimeTypes,
                filenameMaxLength: $filenameMaxLength,
                notFoundMessage: $notFoundMessage,
                notReadableMessage: $notReadableMessage,
                maxSizeMessage: $maxSizeMessage,
                mimeTypesMessage: $mimeTypesMessage,
                disallowEmptyMessage: $disallowEmptyMessage,
                filenameTooLongMessage: $filenameTooLongMessage,
                uploadIniSizeErrorMessage: $uploadIniSizeErrorMessage,
                uploadFormSizeErrorMessage: $uploadFormSizeErrorMessage,
                uploadPartialErrorMessage: $uploadPartialErrorMessage,
                uploadNoFileErrorMessage: $uploadNoFileErrorMessage,
                uploadNoTmpDirErrorMessage: $uploadNoTmpDirErrorMessage,
                uploadCantWriteErrorMessage: $uploadCantWriteErrorMessage,
                uploadExtensionErrorMessage: $uploadExtensionErrorMessage,
                uploadErrorMessage: $uploadErrorMessage,
                groups: $groups,
                payload: $payload,
                extensions: $extensions,
                extensionsMessage: $extensionsMessage,
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
     * Validates that a value is a valid image.
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function validateImage(
        mixed $value,
        ?array $options = null,
        null|int|string $maxSize = null,
        ?bool $binaryFormat = null,
        ?array $mimeTypes = null,
        ?int $filenameMaxLength = null,
        ?int $minWidth = null,
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $minHeight = null,
        null|float|int $maxRatio = null,
        null|float|int $minRatio = null,
        null|float|int $minPixels = null,
        null|float|int $maxPixels = null,
        ?bool $allowSquare = null,
        ?bool $allowLandscape = null,
        ?bool $allowPortrait = null,
        ?bool $detectCorrupted = null,
        ?string $notFoundMessage = null,
        ?string $notReadableMessage = null,
        ?string $maxSizeMessage = null,
        ?string $mimeTypesMessage = null,
        ?string $disallowEmptyMessage = null,
        ?string $filenameTooLongMessage = null,
        ?string $uploadIniSizeErrorMessage = null,
        ?string $uploadFormSizeErrorMessage = null,
        ?string $uploadPartialErrorMessage = null,
        ?string $uploadNoFileErrorMessage = null,
        ?string $uploadNoTmpDirErrorMessage = null,
        ?string $uploadCantWriteErrorMessage = null,
        ?string $uploadExtensionErrorMessage = null,
        ?string $uploadErrorMessage = null,
        ?string $sizeNotDetectedMessage = null,
        ?string $maxWidthMessage = null,
        ?string $minWidthMessage = null,
        ?string $maxHeightMessage = null,
        ?string $minHeightMessage = null,
        ?string $minPixelsMessage = null,
        ?string $maxPixelsMessage = null,
        ?string $maxRatioMessage = null,
        ?string $minRatioMessage = null,
        ?string $allowSquareMessage = null,
        ?string $allowLandscapeMessage = null,
        ?string $allowPortraitMessage = null,
        ?string $corruptedMessage = null,
        ?array $groups = null,
        mixed $payload = null,
        null|array|string $extensions = null,
        ?string $extensionsMessage = null
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\Image(
                options: $options,
                maxSize: $maxSize,
                binaryFormat: $binaryFormat,
                mimeTypes: $mimeTypes,
                filenameMaxLength: $filenameMaxLength,
                minWidth: $minWidth,
                maxWidth: $maxWidth,
                maxHeight: $maxHeight,
                minHeight: $minHeight,
                maxRatio: $maxRatio,
                minRatio: $minRatio,
                minPixels: $minPixels,
                maxPixels: $maxPixels,
                allowSquare: $allowSquare,
                allowLandscape: $allowLandscape,
                allowPortrait: $allowPortrait,
                detectCorrupted: $detectCorrupted,
                notFoundMessage: $notFoundMessage,
                notReadableMessage: $notReadableMessage,
                maxSizeMessage: $maxSizeMessage,
                mimeTypesMessage: $mimeTypesMessage,
                disallowEmptyMessage: $disallowEmptyMessage,
                filenameTooLongMessage: $filenameTooLongMessage,
                uploadIniSizeErrorMessage: $uploadIniSizeErrorMessage,
                uploadFormSizeErrorMessage: $uploadFormSizeErrorMessage,
                uploadPartialErrorMessage: $uploadPartialErrorMessage,
                uploadNoFileErrorMessage: $uploadNoFileErrorMessage,
                uploadNoTmpDirErrorMessage: $uploadNoTmpDirErrorMessage,
                uploadCantWriteErrorMessage: $uploadCantWriteErrorMessage,
                uploadExtensionErrorMessage: $uploadExtensionErrorMessage,
                uploadErrorMessage: $uploadErrorMessage,
                sizeNotDetectedMessage: $sizeNotDetectedMessage,
                maxWidthMessage: $maxWidthMessage,
                minWidthMessage: $minWidthMessage,
                maxHeightMessage: $maxHeightMessage,
                minHeightMessage: $minHeightMessage,
                minPixelsMessage: $minPixelsMessage,
                maxPixelsMessage: $maxPixelsMessage,
                maxRatioMessage: $maxRatioMessage,
                minRatioMessage: $minRatioMessage,
                allowSquareMessage: $allowSquareMessage,
                allowLandscapeMessage: $allowLandscapeMessage,
                allowPortraitMessage: $allowPortraitMessage,
                corruptedMessage: $corruptedMessage,
                groups: $groups,
                payload: $payload,
                extensions: $extensions,
                extensionsMessage: $extensionsMessage,
            ),
        ]);
    }

    /**
     * Validates the length of a given value.
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
     */
    public function validateLength(
        mixed $value,
        null|array|int $exactly = null,
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

    /**
     * Validates that a password meets given constraints.
     */
    public function validatePassword(
        mixed   $value,
        ?int    $min = null,
        ?int    $max = null,
        ?string $emptyMessage = null,
        ?string $minMessage = null,
        ?string $maxMessage = null,
        ?string $regexMessage = null,
    ): ConstraintViolationListInterface {
        $validator = self::getValidator();

        return $validator->validate($value, [
            new Constraints\NotBlank(
                message: $emptyMessage,
            ),
            new Constraints\Length(
                min: $min,
                max: $max,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
            ),
            new Constraints\Regex(
                pattern: '/^(?=.*[a-z])(?=.*[0-9]).+$/iu',
                message: $regexMessage,
            ),
        ]);
    }

    /**
     * Validates that a value is within a given range.
     *
     * @SuppressWarnings("PHPMD.ExcessiveParameterList")
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
            new Constraints\NotBlank(
                message: $message,
            ),
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
     * @return Constraint[]
     * @throws Mage_Core_Exception
     */
    public function getContraintsByType(string $type, array $options = []): array
    {
        $message = $options['message'] ?? null;

        return match ($type) {
            'Alnum'         => [new Constraints\Type(type: 'alnum', message: $message)],
            'Alpha'         => [new Constraints\Type(type: 'alpha', message: $message)],
            'Between'       => [new Constraints\Range(min: $options['min'] ?? null, max: $options['max'] ?? null)],
            'Callback'      => [new Constraints\Callback($options)],
            'Ccnum'         => [new Constraints\Luhn($options)],
            'CreditCard'    => [new Constraints\CardScheme($options)],
            'Date'          => [new Constraints\Date($options)],
            'Digits'        => [new Constraints\Type(type: 'digit', message: $message)],
            'Float'         => [new Constraints\Type(type: 'float', message: $message)],
            'GreaterThan'   => [new Constraints\GreaterThan($options)],
            'Hostname'      => [new Constraints\Hostname($options)],
            'Iban'          => [new Constraints\Iban($options)],
            'Identical'     => [new Constraints\IdenticalTo($options)],
            'InArray'       => [new Constraints\Choice($options)],
            'Int'           => [new Constraints\Type(type: 'int', message: $message)],
            'Ip'            => [new Constraints\Ip($options)],
            'Isbn'          => [new Constraints\Isbn($options)],
            'LessThan'      => [new Constraints\LessThan($options)],
            'NoEmpty'       => [new Constraints\NotBlank($options)],
            'Regex'         => [new Constraints\Regex(pattern: $options['pattern'] ?? '')],
            'StringLength'  => [new Constraints\Length($options)],
            default         => throw new Mage_Core_Exception("Validator $type does not exist")
        };
    }

    public function getErrorMessages(array|ArrayObject $violations): ?ArrayObject
    {
        $errors = new ArrayObject();
        foreach ($violations as $violation) {
            if ($violation instanceof ConstraintViolationListInterface) {
                foreach ($violation as $error) {
                    $errors->append($error->getMessage());
                }
            }
        }

        if (count($errors) === 0) {
            return null;
        }

        return $errors;
    }
}
