# BakeryCart - E-Commerce Platform for Easy Bake Bakery

## Project Overview

BakeryCart is a full-featured e-commerce platform built for **Easy Bake (@easybake.bh)**, a wholesale French bakery in Bahrain. This is a learning project focused on mastering Laravel 12 + Next.js 15 with production-grade architecture.

**Status**: Database schema and models completed. Currently ready for factories, seeders, and API development.

---

## Tech Stack

### Backend
- **Laravel 12** (PHP 8.3+)
- **PostgreSQL 16** (for JSONB support and advanced features)
- **Redis** (caching and session storage)
- **Laravel Sanctum** (cookie-based session authentication for SPAs)
- **Laravel Filament 3** (admin panel)
- **Spatie Laravel Query Builder** (API filtering and sorting)
- **Spatie Laravel Permission** (roles and permissions)

### Frontend (Planned)
- **Next.js 15** (App Router)
- **TypeScript**
- **shadcn/ui** (component library)
- **Tailwind CSS**
- **Zustand** (cart state management)
- **TanStack Query / React Query** (server state management)

### Development Environment
- **Docker Compose** for PostgreSQL + Redis (local development)
- **Herd** (local Laravel development)

---

## Business Context

### About Easy Bake
- **Brand**: "France Factory" (@easybake.bh on Instagram)
- **Positioning**: Wholesale French bakery with premium artisan positioning
- **Products**: Croissants, French bread, baguettes, sourdough
- **Operating Hours**: 6:00 AM – 2:00 PM, Saturday-Thursday (closed Friday)
- **Location**: SAAR-Maqabah, Block 509, Bahrain
- **Customer Segments**: 
  - B2C: Individual consumers ordering for home delivery/pickup
  - B2B: Cafes, restaurants, hotels (wholesale orders - future phase)

### Market Context (Bahrain)
- **Currency**: BHD (Bahraini Dinar) - uses 3 decimal places (0.500, 1.250)
- **VAT**: 10% tax added at checkout (not included in prices)
- **Language**: Bilingual (Arabic + English required)
- **Mobile-first**: 68.52% of transactions on smartphones

---

## Architecture Principles

### Code Standards (CRITICAL - ALWAYS FOLLOW)

1. **Strict Typing**: ALWAYS use `declare(strict_types=1);` at the top of every PHP file
2. **Final Classes**: ALL models, services, and controllers MUST be `final` classes
3. **Type Declarations**: ALWAYS use explicit return types and parameter types
4. **PSR-12**: Follow PSR-12 coding standards
5. **No Enums in Migrations**: Use `string` instead of `enum` for flexibility
6. **SOLID Principles**: Follow SOLID principles for OOP

### Project Structure
```
app/
├── Models/           # Eloquent models (relationships, casts, simple scopes only)
├── Services/         # Business logic (complex queries, transactions, calculations)
├── Http/
│   ├── Controllers/  # Thin controllers (delegate to services)
│   └── Requests/     # Form validation
└── ...

database/
├── migrations/       # Database schema
├── seeders/         # Data seeders
└── factories/       # Model factories
```

### Layer Responsibilities

**Models** (Lean - Data Layer):
- ✅ Relationships (`belongsTo`, `hasMany`, etc.)
- ✅ Casts (type conversions)
- ✅ Accessors/Mutators (computed attributes)
- ✅ Simple scopes (`scopeActive`, `scopeAvailable`)
- ❌ NO complex business logic
- ❌ NO multi-step operations

**Services** (Business Logic Layer):
- ✅ Complex queries with multiple conditions
- ✅ Transactions
- ✅ Multi-step operations
- ✅ Calculations (discounts, taxes, shipping)
- ✅ Stock management
- ✅ Order processing

**Controllers** (Presentation Layer):
- ✅ Use Spatie Query Builder for filtering/sorting
- ✅ Delegate to services for business logic
- ✅ Return JSON responses
- ✅ Keep thin (5-20 lines per method)
- ❌ NO business logic in controllers

---

## Database Schema

### Complete Table List (30 Tables)

**Foundation (8 tables)**
1. `users` - User accounts
2. `addresses` - User delivery addresses
3. `categories` - Product categories (self-referencing)
4. `category_translations` - Category names/descriptions in en/ar
5. `tax_rates` - VAT and tax configuration
6. `delivery_zones` - Delivery areas with fees
7. `delivery_zone_areas` - Areas within zones
8. `business_hours` - Store operating hours
9. `special_closures` - Holidays/closures
10. `settings` - Key-value configuration

**Products (4 tables)**
11. `products` - Main product catalog
12. `product_translations` - Product names/descriptions in en/ar
13. `product_images` - Product photos (multiple per product)
14. `product_variants` - Pack sizes (1pc, 6-pack, 12-pack)
15. `product_price_history` - Price change audit trail

**Shopping (3 tables)**
16. `carts` - Shopping carts (user + guest)
17. `cart_items` - Items in cart
18. `wishlists` - Saved products

**Orders (7 tables)**
19. `orders` - Order header (denormalized snapshots)
20. `order_items` - Products in order (with snapshots)
21. `order_status_histories` - Status change tracking
22. `order_cancellations` - Cancellation/refund data
23. `coupons` - Discount codes
24. `coupon_usages` - Track who used which coupon
25. `payments` - Payment transactions (mocked for now)

**Reviews (2 tables)**
26. `reviews` - Product reviews (1-5 stars)
27. `review_images` - Customer photos with reviews

**Spatie Permission (5 tables)**
28. `roles`
29. `permissions`
30. `model_has_roles`
31. `model_has_permissions`
32. `role_has_permissions`

---

## Key Design Decisions

### 1. Translation Strategy
- Separate tables for translations (`*_translations`)
- NOT JSON blobs in main table
- Supports unlimited languages (just add rows)
- Example: `product_translations` with `locale` field ('en', 'ar')

### 2. Product Variants
- Separate SKU, price, and stock per variant
- Pack quantities: 1pc, 6-pack, 12-pack
- If product has variants, customers buy variants (not base product)
- Full price stored (not price adjustment)

### 3. Order Snapshots (Denormalization)
- Orders store BOTH `shipping_address_id` (reference) AND snapshot fields
- Product name, price, SKU copied to `order_items`
- Preserves historical data even if products/addresses deleted

### 4. Price History
- `product_price_history` tracks all price changes
- Audit trail for compliance and analytics
- Tracks who changed price and why

### 5. Coupon Usage Tracking
- `coupon_usages` table tracks every use
- Can audit which user used which coupon on which order
- Prevents fraud and tracks ROI

### 6. Guest Carts
- Carts support both `user_id` (logged in) and `session_id` (guest)
- Guest carts merge into user cart on login
- Expire after 7 days

### 7. Mock Payments
- `payments` table ready for future gateway integration
- `is_mock = true` for testing
- Supports: cash, card, benefit_pay, bank_transfer

---

## Models Created (26 Models)

**All models follow strict standards:**
- ✅ `declare(strict_types=1);`
- ✅ `final class`
- ✅ Explicit return types
- ✅ Proper relationships
- ✅ Type casts

**Model List:**
1. Address
2. BusinessHour
3. Cart
4. CartItem
5. Category
6. CategoryTranslation
7. Coupon
8. CouponUsage
9. DeliveryZone
10. DeliveryZoneArea
11. Order
12. OrderCancellation
13. OrderItem
14. OrderStatusHistory
15. Payment
16. Product
17. ProductImage
18. ProductPriceHistory
19. ProductTranslation
20. ProductVariant
21. Review
22. ReviewImage
23. Setting
24. SpecialClosure
25. TaxRate
26. Wishlist

---

## Services Created (4 Services)

All services are `final readonly` classes with business logic.

### 1. ProductService
**Responsibilities:**
- Get available/featured/popular products
- Search products (multi-language)
- Filter by price range, category
- Check product availability
- Increment views/sales counters
- Manage stock (increment/decrement)

**Key Methods:**
```php
getAvailableQuery(): Builder
getFeatured(int $limit = 10): Collection
search(string $term, string $locale = 'en'): Builder
isAvailableForPurchase(Product $product, int $quantity = 1): bool
incrementViews(Product $product): void
decrementStock(Product $product, int $quantity): bool
```

### 2. CartService
**Responsibilities:**
- Get/create cart for user or guest
- Add/update/remove items
- Calculate cart totals
- Merge guest cart into user cart (on login)
- Clear cart

**Key Methods:**
```php
getOrCreateCart(?User $user, ?string $sessionId): Cart
addItem(Cart $cart, Product $product, ?ProductVariant $variant, int $quantity): CartItem
updateQuantity(CartItem $item, int $quantity): bool
calculateTotals(Cart $cart): array
mergeGuestCart(Cart $guestCart, Cart $userCart): void
```

### 3. OrderService
**Responsibilities:**
- Create order from cart
- Validate stock availability
- Apply coupons
- Calculate taxes and shipping
- Update order status
- Cancel orders and process refunds
- Generate order numbers

**Key Methods:**
```php
createFromCart(?User $user, array $data): Order
updateStatus(Order $order, string $newStatus, ?User $user, ?string $notes): bool
cancel(Order $order, string $reason, string $cancelledBy): bool
processRefund(OrderCancellation $cancellation, string $transactionId): bool
```

### 4. AddressService
**Responsibilities:**
- CRUD operations for addresses
- Set default address
- Calculate delivery fees by zone
- Get delivery zone for address

**Key Methods:**
```php
create(User $user, array $data): Address
update(Address $address, array $data): Address
delete(Address $address): bool
getDeliveryZone(Address $address): ?DeliveryZone
calculateDeliveryFee(Address $address, float $cartTotal): float
```

---

## Authentication & Authorization

### Roles (Spatie Permission)
1. **customer** (default) - Browse, order, review
2. **staff** - Manage products, process orders, moderate reviews
3. **admin** - Full access to everything

### Authentication
- **Web/Mobile**: Sanctum cookie-based sessions
- **API**: Sanctum personal access tokens (future)
- Guest checkout supported

### Key Permissions
```
Product: view, create, edit, delete
Order: view, manage, cancel, view all
Customer: view, edit, delete
Review: view, moderate, respond
Coupon: view, manage
Settings: view, manage
```

---

## Environment Configuration

### Required .env Variables
```env
APP_NAME=BakeryCart
APP_ENV=local
APP_TIMEZONE=Asia/Bahrain
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bakerycart
DB_USERNAME=postgres
DB_PASSWORD=secret

CACHE_STORE=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost:3000
```

---

## Naming Conventions

### Database
- Tables: `snake_case`, plural (e.g., `product_variants`)
- Columns: `snake_case` (e.g., `is_available`)
- Foreign keys: `model_id` (e.g., `category_id`)
- Pivot tables: alphabetical order (e.g., `model_has_roles`)

### PHP
- Classes: `PascalCase` (e.g., `ProductService`)
- Methods: `camelCase` (e.g., `getAvailableProducts()`)
- Variables: `camelCase` (e.g., `$productService`)
- Constants: `SCREAMING_SNAKE_CASE` (e.g., `TAX_RATE`)

### Files
- Models: Singular `PascalCase` (e.g., `Product.php`)
- Controllers: Singular + `Controller` (e.g., `ProductController.php`)
- Services: Singular + `Service` (e.g., `ProductService.php`)
- Migrations: `create_table_name_table` (e.g., `create_products_table.php`)

---

## Common Tasks & Examples

### Adding a New Feature

1. **Migration** (if DB changes needed)
```bash
php artisan make:migration create_feature_table
```

2. **Model** (if new entity)
```bash
php artisan make:model Feature
```
Add: `declare(strict_types=1);`, `final class`, relationships, casts

3. **Service** (if complex logic)
```bash
# Create manually in app/Services/
```
Add: `declare(strict_types=1);`, `final readonly class`, business methods

4. **Controller** (API endpoint)
```bash
php artisan make:controller Api/FeatureController
```
Use Spatie Query Builder, delegate to service

### Using Spatie Query Builder
```php
// In Controller
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

public function index(): JsonResponse
{
    $products = QueryBuilder::for(Product::class)
        ->allowedFilters([
            'is_available',
            AllowedFilter::exact('category_id'),
            AllowedFilter::callback('search', fn($query, $value) => 
                $this->productService->search($value)
            ),
        ])
        ->allowedSorts(['price', 'created_at', 'sales_count'])
        ->allowedIncludes(['category', 'translations', 'images'])
        ->paginate();

    return response()->json($products);
}
```

### Creating Relationships
```php
// One-to-Many
public function items(): HasMany
{
    return $this->hasMany(OrderItem::class);
}

// Belongs To
public function product(): BelongsTo
{
    return $this->belongsTo(Product::class);
}

// Many-to-Many (with Spatie)
// Use Spatie's HasRoles trait instead
```

---

## Testing Strategy (Future)

### Factory Pattern
```bash
php artisan make:factory ProductFactory
```

### Seeder Pattern
```bash
php artisan make:seeder ProductSeeder
```

### Test Structure
- Feature tests for API endpoints
- Unit tests for services
- Database factories for test data

---

## API Response Format

### Success Response
```json
{
  "data": {
    "id": 1,
    "name": "Butter Croissant",
    "price": "0.500"
  }
}
```

### Paginated Response
```json
{
  "data": [...],
  "links": {...},
  "meta": {
    "current_page": 1,
    "total": 50
  }
}
```

### Error Response
```json
{
  "message": "Product not found",
  "errors": {
    "product_id": ["The selected product is invalid."]
  }
}
```

---

## What's Next (Roadmap)

### Phase 1: Data Layer (COMPLETED ✅)
- ✅ Database migrations (30 tables)
- ✅ Eloquent models (26 models)
- ✅ Service classes (4 services)
- ✅ Relationships and business logic

### Phase 2: Data Generation (CURRENT)
- ⏳ Factories for all models
- ⏳ Seeders with realistic data
- ⏳ Test data generation

### Phase 3: API Development
- ⏳ API controllers with Spatie Query Builder
- ⏳ Form Request validation
- ⏳ API routes
- ⏳ Authentication endpoints

### Phase 4: Admin Panel
- ⏳ Filament resources
- ⏳ Custom admin pages
- ⏳ Dashboard widgets

### Phase 5: Frontend (Next.js)
- ⏳ Next.js 15 setup
- ⏳ shadcn/ui components
- ⏳ API integration
- ⏳ Cart and checkout flow

### Phase 6: Payment Integration
- ⏳ Payment gateway (Stripe/Tap/BenefitPay)
- ⏳ Webhook handling
- ⏳ Refund processing

### Phase 7: Production
- ⏳ Deployment setup
- ⏳ Performance optimization
- ⏳ Security hardening
- ⏳ Monitoring and logging

---

## Important Notes for AI Assistant

### Always Remember:
1. **Use strict typing**: `declare(strict_types=1);` on ALL PHP files
2. **Make classes final**: `final class ModelName extends Model`
3. **Explicit return types**: Always declare return types
4. **Follow the layer pattern**: Models (data) → Services (logic) → Controllers (presentation)
5. **Use Spatie Query Builder** in controllers for filtering
6. **Keep models lean**: Only relationships, casts, simple scopes
7. **BHD currency**: Use 3 decimal places (e.g., `decimal:3`)
8. **Bilingual**: Support both English and Arabic (separate translation tables)

### Never:
- ❌ Use enum in migrations (use string instead)
- ❌ Put business logic in models
- ❌ Put business logic in controllers
- ❌ Use raw SQL queries (use Eloquent/Query Builder)
- ❌ Hardcode values (use constants or settings)
- ❌ Skip type declarations

### Code Style:
- PSR-12 compliant
- Clean, readable code
- DRY (Don't Repeat Yourself)
- SOLID principles
- Descriptive variable names
- Comments for complex logic only

---

## Quick Reference Commands
```bash
# Migrations
php artisan make:migration create_table_name_table
php artisan migrate
php artisan migrate:fresh --seed

# Models
php artisan make:model ModelName

# Factories
php artisan make:factory ModelNameFactory

# Seeders
php artisan make:seeder ModelNameSeeder

# Controllers
php artisan make:controller Api/ModelNameController

# Check database
php artisan db:show
php artisan db:table table_name

# Cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Tinker
php artisan tinker
```

---

## Contact & Resources

- **Project Lead**: DonPollo
- **Backend**: Laravel 12 (latest)
- **Database**: PostgreSQL 16
- **Docs**: https://laravel.com/docs/12.x
- **Spatie Query Builder**: https://spatie.be/docs/laravel-query-builder
- **Spatie Permission**: https://spatie.be/docs/laravel-permission

---

**Last Updated**: January 2026
**Project Status**: Models & Services Complete - Ready for Factories & API Development