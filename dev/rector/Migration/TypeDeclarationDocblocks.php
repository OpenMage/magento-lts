<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Rector
 */

declare(strict_types=1);

namespace OpenMage\Rector\Migration;

use Rector\TypeDeclarationDocblocks\Rector\Class_\AddVarArrayDocblockFromDimFetchAssignRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\AddReturnArrayDocblockFromDataProviderParamRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\ClassMethodArrayDocblockParamFromLocalCallsRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromGetterReturnRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarArrayFromPropertyDefaultsRector;
use Rector\TypeDeclarationDocblocks\Rector\Class_\DocblockVarFromParamDocblockInConstructorRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockFromAssignsParamToParamReferenceRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddParamArrayDocblockFromDataProviderRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForCommonObjectDenominatorRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForDimFetchArrayFromAssignsRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForJsonArrayRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockFromMethodCallDocblockRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector;

final class TypeDeclarationDocblocks
{
    /**
     * @return class-string[]
     */
    public static function getRules(): array
    {
        return [
            AddVarArrayDocblockFromDimFetchAssignRector::class,
            AddReturnArrayDocblockFromDataProviderParamRector::class,
            ClassMethodArrayDocblockParamFromLocalCallsRector::class,
            DocblockVarArrayFromGetterReturnRector::class,
            DocblockVarArrayFromPropertyDefaultsRector::class,
            DocblockVarFromParamDocblockInConstructorRector::class,
            AddParamArrayDocblockFromAssignsParamToParamReferenceRector::class,
            AddParamArrayDocblockFromDataProviderRector::class,
            AddReturnDocblockForArrayDimAssignedObjectRector::class,
            AddReturnDocblockForCommonObjectDenominatorRector::class,
            AddReturnDocblockForDimFetchArrayFromAssignsRector::class,
            AddReturnDocblockForJsonArrayRector::class,
            AddReturnDocblockFromMethodCallDocblockRector::class,
            DocblockReturnArrayFromDirectArrayInstanceRector::class,
        ];
    }
}
