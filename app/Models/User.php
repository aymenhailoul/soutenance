<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'password',
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
            'password' => 'hashed',
        ];
    }

    public function getAuthIdentifierName()
    {
        return 'name';
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'user_permissions');
    }

    public function hasPageAccess($route)
    {
        // 1. Direct permission check
        if ($this->pages()->where('route', $route)->exists()) {
            return true;
        }

        // 2. Special Case: Page Management linked to Users Management
        if (str_starts_with($route, 'pages.') && $this->pages()->where('route', 'users.index')->exists()) {
            return true;
        }

        // 3. Resource-based inheritance logic
        $parts = explode('.', $route);
        if (count($parts) >= 2) {
            $resource = $parts[0]; // e.g., 'products'
            $action = end($parts);   // e.g., 'create', 'edit'

            // EXCEPTION: Products (Strict Security)
            // For products, 'index' ONLY grants view access.
            // 'create', 'edit', 'delete' require 'products.create'.
            if ($resource === 'products') {
                // Read-only actions allowed by index
                $readActions = ['index', 'show', 'export'];
                if (in_array($action, $readActions) || str_starts_with($action, 'search')) {
                    if ($this->pages()->where('route', 'products.index')->exists()) {
                        return true;
                    }
                }

                // Write actions allowed by create
                $writeActions = ['create', 'store', 'edit', 'update', 'destroy'];
                if (in_array($action, $writeActions)) {
                    if ($this->pages()->where('route', 'products.create')->exists()) {
                        return true;
                    }
                    // Explicitly DENY if they don't have create permission, do not fall through to generic rule
                    return false;
                }

                // For any other action (like show_cost) that wasn't caught above, DENY access if relying on inheritance.
                // It must be granted explicitly via Direct Check (Step 1).
                return false;
            }

            // GENERIC DEFAULT RULE (Clients, Employees, Facturation, etc.)
            // For these resources, having 'index' permission implies full access to all sub-actions.
            if ($this->pages()->where('route', $resource . '.index')->exists()) {
                return true;
            }

            // Also allow if specific Create/Edit permission exists (Forward compatibility)
            if ($action === 'store' && $this->pages()->where('route', $resource . '.create')->exists())
                return true;
            if ($action === 'update' && $this->pages()->where('route', $resource . '.edit')->exists())
                return true;
        }

        return false;
    }
}
