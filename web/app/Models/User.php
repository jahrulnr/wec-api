<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;
use MongoDB\Laravel\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    
    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withTimestamps();
    }
    
    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()
            ->where('name', $roleName)
            ->where('is_active', true)
            ->exists();
    }
    
    /**
     * Check if the user has any of the given roles.
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()
            ->whereIn('name', $roleNames)
            ->where('is_active', true)
            ->exists();
    }
    
    /**
     * Check if the user has all the given roles.
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasAllRoles(array $roleNames): bool
    {
        return $this->roles()
            ->whereIn('name', $roleNames)
            ->where('is_active', true)
            ->count() === count($roleNames);
    }
    
    /**
     * Check if the user has a specific permission through their roles.
     *
     * @param string $permissionSlug
     * @return bool
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()
            ->where('is_active', true)
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }
    
    /**
     * Assign roles to the user.
     *
     * @param array|int $roles
     * @return void
     */
    public function assignRoles($roles): void
    {
        $this->roles()->sync($roles, false);
    }
    
    /**
     * Remove roles from the user.
     *
     * @param array|int $roles
     * @return void
     */
    public function removeRoles($roles): void
    {
        $this->roles()->detach($roles);
    }
}
