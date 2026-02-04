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
            if ($resource === 'products') {
                // Read-only actions allowed by index
                $readActions = ['index', 'show'];
                if (in_array($action, $readActions) || str_starts_with($action, 'search')) {
                    if ($this->pages()->where('route', 'products.index')->exists()) {
                        return true;
                    }
                }

                // Write actions
                if (in_array($action, ['create', 'store'])) {
                    return $this->pages()->where('route', 'products.create')->exists();
                }

                if (in_array($action, ['edit', 'update'])) {
                    return $this->pages()->where('route', 'products.edit')->exists();
                }

                if ($action === 'destroy') {
                    return $this->pages()->where('route', 'products.destroy')->exists();
                }

                return false;
            }

            // EXCEPTION: Ventes (cancel and return require explicit permissions)
            if ($resource === 'ventes') {
                // Read-only actions allowed by index
                $readActions = ['index', 'show', 'export'];
                if (in_array($action, $readActions)) {
                    if ($this->pages()->where('route', 'ventes.index')->exists()) {
                        return true;
                    }
                }

                // Cancel and return require explicit permissions
                if ($action === 'cancel') {
                    return $this->pages()->where('route', 'ventes.cancel')->exists();
                }
                if ($action === 'return') {
                    return $this->pages()->where('route', 'ventes.return')->exists();
                }

                // For any other action, DENY if relying on inheritance
                return false;
            }

            // EXCEPTION: Facturation (split into services and products)
            // facturation.services.* routes need facturation.services.index permission
            // facturation.produits.* routes need facturation.produits.index permission
            if ($resource === 'facturation') {
                // Check for services sub-routes
                if (count($parts) >= 3 && $parts[1] === 'services') {
                    if ($this->pages()->where('route', 'facturation.services.index')->exists()) {
                        return true;
                    }
                    return false;
                }
                // Check for products sub-routes
                if (count($parts) >= 3 && $parts[1] === 'produits') {
                    if ($this->pages()->where('route', 'facturation.produits.index')->exists()) {
                        return true;
                    }
                    return false;
                }
                // For other facturation routes (pdf, cancel), check for either permission
                if ($this->pages()->where('route', 'facturation.services.index')->exists() ||
                    $this->pages()->where('route', 'facturation.produits.index')->exists()) {
                    return true;
                }
                return false;
            }

            // GENERIC DEFAULT RULE (Clients, Employees, etc.)
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

        // Special case: credit-notes access is granted if user has ventes.index permission
        if (str_starts_with($route, 'credit-notes.') && $this->pages()->where('route', 'ventes.index')->exists()) {
            return true;
        }

        // Special case: vehicles access is granted if user has clients.index permission
        if (str_starts_with($route, 'vehicles.') && $this->pages()->where('route', 'clients.index')->exists()) {
            return true;
        }

        return false;
    }
}
