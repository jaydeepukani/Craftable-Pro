<?php

namespace Brackets\CraftablePro\Models;

use Brackets\CraftablePro\Database\Factories\CraftableProUserFactory;
use Brackets\CraftablePro\Helpers\Initials;
use Brackets\CraftablePro\Media\AutoProcessMediaTrait;
use Brackets\CraftablePro\Media\HasMediaPreviewsTrait;
use Brackets\CraftablePro\Media\InteractsWithMedia;
use Brackets\CraftablePro\Media\ProcessMediaTrait;
use Brackets\CraftablePro\Notifications\CraftableProVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 */
class BaseCraftableProUser extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;
    use ProcessMediaTrait;
    use AutoProcessMediaTrait;
    use HasMediaPreviewsTrait;
    use HasRoles;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $guard = 'craftable-pro';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'locale',
        'active',
        'last_active_at',
        'invitation_sent_at',
        'invitation_accepted_at',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    protected $appends = ['resource_url', 'avatar', 'avatar_url', 'media_details', 'has_enabled_two_factor_authentication'];

    protected static function newFactory()
    {
        return CraftableProUserFactory::new();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CraftableProVerifyEmail());
    }

    /**
     * Get the user's initials.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Initials::new()->generate($this->first_name . ' ' . $this->last_name)
        );
    }

    /**
     * Get the user's avatar media object.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getMedia('avatar')
        );
    }

    /**
     * Get the user's avatar URL.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->getFirstMediaUrl('avatar', 'thumb')
        );
    }

    /**
     * Get the user's resource url.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function resourceUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->id) {
                    return route('craftable-pro.craftable-pro-users.show', $this->id);
                }

                return null;
            }
        );
    }

    public function wasInvited()
    {
        return $this->invitation_sent_at !== null && $this->invitation_accepted_at === null;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->acceptsMimeTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/svg+xml'])
            ->maxFileSize(2 * 1024 * 1024)
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->autoRegisterPreviews();

        if (! class_exists('Spatie\Image\Enums\CropPosition')) {
            $this->addMediaConversion('thumb')
                ->crop('crop-center', 40, 40)
                ->performOnCollections('avatar');
        } else {
            $this->addMediaConversion('thumb')
                ->crop(40, 40, \Spatie\Image\Enums\CropPosition::Center)
                ->performOnCollections('avatar');
        }
    }

    public function getHasEnabledTwoFactorAuthenticationAttribute(): bool
    {
        return $this->hasEnabledTwoFactorAuthentication();
    }

    public function getHasRequiredTwoFactorAuthenticationAttribute(): bool
    {
        return $this->roles()->where('two_factor_auth_required', '=', true)->exists();
    }
}
