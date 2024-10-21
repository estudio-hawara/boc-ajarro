<?php

namespace App\Filament\Admin\Resources\PageResource\RelationManagers\LinksRelationManager;

use App\Http\BocUrl;
use Illuminate\Database\Eloquent\Model;

trait Permissions
{
    // @codeCoverageIgnoreStart

    /**
     * Whether the relation manager should be shown or not.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if ($ownerRecord->name == BocUrl::BulletinArticle->name) {
            return false;
        }

        return parent::canViewForRecord($ownerRecord, $pageClass);
    }

    // @codeCoverageIgnoreEnd
}