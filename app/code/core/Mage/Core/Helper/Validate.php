<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Validation
 */
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IdenticalTo;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Luhn;
use Symfony\Component\Validator\Constraints\CardScheme;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Hostname;
use Symfony\Component\Validator\Constraints\Iban;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Isbn;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraint;
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
            new Choice(
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
            new Count(
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
        $constraints[] = new Date(
            options: $options,
            message: $message,
            groups: $groups,
            payload: $payload,
        );

        if (!$empty) {
            $constraints[] = new NotBlank(
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
        $constraints[] = new DateTime(
            format: $format,
            message: $message,
            groups: $groups,
            payload: $payload,
            options: $options,
        );

        if (!$empty) {
            $constraints[] = new NotBlank(
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
            new NotBlank(
                message: $message,
            ),
            new Email(
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
            new File(
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
            new IdenticalTo(
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
            new Image(
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
            new Length(
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
            new NotBlank(
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
            new NotBlank(
                message: $emptyMessage,
            ),
            new Length(
                min: $min,
                max: $max,
                minMessage: $minMessage,
                maxMessage: $maxMessage,
            ),
            new Regex(
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
            new Range(
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
            new Regex(
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
            new Type(
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
            new NotBlank(
                message: $message,
            ),
            new Url(
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
            'Alnum'         => [new Type(type: 'alnum', message: $message)],
            'Alpha'         => [new Type(type: 'alpha', message: $message)],
            'Between'       => [new Range(min: $options['min'] ?? null, max: $options['max'] ?? null)],
            'Callback'      => [new Callback($options)],
            'Ccnum'         => [new Luhn($options)],
            'CreditCard'    => [new CardScheme($options)],
            'Date'          => [new Date($options)],
            'Digits'        => [new Type(type: 'digit', message: $message)],
            'Float'         => [new Type(type: 'float', message: $message)],
            'GreaterThan'   => [new GreaterThan($options)],
            'Hostname'      => [new Hostname($options)],
            'Iban'          => [new Iban($options)],
            'Identical'     => [new IdenticalTo($options)],
            'InArray'       => [new Choice($options)],
            'Int'           => [new Type(type: 'int', message: $message)],
            'Ip'            => [new Ip($options)],
            'Isbn'          => [new Isbn($options)],
            'LessThan'      => [new LessThan($options)],
            'NoEmpty'       => [new NotBlank($options)],
            'Regex'         => [new Regex(pattern: $options['pattern'] ?? '')],
            'StringLength'  => [new Length($options)],
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
