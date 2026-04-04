<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration;

use Rector\TypeDeclarationDocblocks\Rector;

final class TypeDeclarationDocblocks
{
    /**
     * @return class-string[]
     */
    public static function getRules(): array
    {
        return [
            Rector\Class_\AddVarArrayDocblockFromDimFetchAssignRector::class,
            Rector\Class_\AddReturnArrayDocblockFromDataProviderParamRector::class,
            Rector\Class_\ClassMethodArrayDocblockParamFromLocalCallsRector::class,
            Rector\Class_\DocblockVarArrayFromGetterReturnRector::class,
            Rector\Class_\DocblockVarArrayFromPropertyDefaultsRector::class,
            Rector\Class_\DocblockVarFromParamDocblockInConstructorRector::class,
            Rector\ClassMethod\AddParamArrayDocblockFromAssignsParamToParamReferenceRector::class,
            Rector\ClassMethod\AddParamArrayDocblockFromDataProviderRector::class,
            Rector\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector::class,
            Rector\ClassMethod\AddReturnDocblockForCommonObjectDenominatorRector::class,
            Rector\ClassMethod\AddReturnDocblockForDimFetchArrayFromAssignsRector::class,
            Rector\ClassMethod\AddReturnDocblockForJsonArrayRector::class,
            Rector\ClassMethod\AddReturnDocblockFromMethodCallDocblockRector::class,
            Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector::class,
        ];
    }
}
