<?php

namespace App\Filament\Teacher\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    protected function getRedirectUrl(): ?string
    {
        return filament()->getUrl();
    }
}
