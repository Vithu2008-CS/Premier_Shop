# Contributing to Premier Shop

Thank you for your interest in contributing to Premier Shop! This document provides guidelines and instructions for getting started with development.

## Table of Contents

- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Code Standards](#code-standards)
- [Testing](#testing)
- [Git Workflow](#git-workflow)
- [Pull Request Process](#pull-request-process)
- [Reporting Issues](#reporting-issues)

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer 2.x
- Node.js 18+ & npm
- MariaDB 10.x or MySQL 8.x
- Git

## Development Setup

### 1. Clone the Repository

```bash
git clone https://github.com/Vithu2008-CS/Premier_Shop.git
cd Premier_Shop
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
php artisan storage:link
```

Edit `.env` with your local database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=premier_shop_dev
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Setup Database

```bash
php artisan migrate --seed
```

### 6. Run Development Server

```bash
# Terminal 1: Laravel development server
php artisan serve

# Terminal 2: Vite hot reload
npm run dev

# Terminal 3 (optional): Queue listener
php artisan queue:listen
```

The application will be available at `http://localhost:8000`

### 7. Verify Setup

- Visit `http://localhost:8000` and you should see the homepage
- Admin panel: `http://localhost:8000/admin` (use seeded credentials)
- Database migrations should be complete

## Code Standards

### PHP Code Style

We follow **PSR-12** coding standards.

#### Format Code

```bash
./vendor/bin/pint
```

#### Check Without Fixing

```bash
./vendor/bin/pint --test
```

### Best Practices

1. **Type Hints**: Always use type hints on function parameters and return types
   ```php
   public function store(Request $request): JsonResponse
   {
       // ...
   }
   ```

2. **Documentation**: Add comments to complex business logic
   ```php
   // FIXED: Use atomic where() clause to prevent race condition
   $updated = Product::where('id', $id)
       ->where('stock', '>=', $quantity)
       ->decrement('stock', $quantity);
   ```

3. **Eager Loading**: Always use `with()` to prevent N+1 queries
   ```php
   // ❌ Bad: N+1 query problem
   $products = Product::all();
   foreach ($products as $product) {
       echo $product->category->name; // Extra query per product
   }

   // ✅ Good: Eager loading
   $products = Product::with('category')->get();
   ```

4. **Validation**: Use form request classes
   ```php
   // app/Http/Requests/StoreProductRequest.php
   public function rules(): array
   {
       return [
           'name' => 'required|string|max:255',
           'price' => 'required|numeric|min:0',
       ];
   }
   ```

5. **HTML Escaping**: Always escape user input before output
   ```php
   // ❌ Bad: XSS vulnerability
   $review->admin_reply = $request->admin_reply;

   // ✅ Good: Escape HTML
   $review->admin_reply = e($request->admin_reply);
   ```

6. **Transactions**: Use DB transactions for multi-step operations
   ```php
   $order = DB::transaction(function () {
       // Create order
       // Decrement stock
       // Update points
       // Return order
   });
   ```

### Naming Conventions

- **Files**: PascalCase for classes (e.g., `ProductController.php`)
- **Functions**: camelCase (e.g., `purchasableCartItems()`)
- **Database**: snake_case (e.g., `is_age_restricted`)
- **Constants**: UPPER_CASE (e.g., `DEFAULT_PAGINATION`)
- **Branches**: kebab-case (e.g., `fix/n-plus-one-queries`)

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage

# Run with filter
php artisan test --filter=OrderTest
```

### Writing Tests

Create tests in `tests/Feature/` for integration tests:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    /**
     * Test that a user can complete checkout with valid items
     */
    public function test_user_can_checkout_with_valid_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 100]);

        $this->actingAs($user)
            ->post('/checkout/process', [
                'address_line' => '123 Main St',
                'city' => 'London',
                'phone' => '01234567890',
                'payment_method' => 'Bank Transfer',
                'items' => [1],
            ])
            ->assertRedirect('/orders/1');

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    /**
     * Test that checkout fails when stock is insufficient
     */
    public function test_checkout_fails_with_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);

        $this->actingAs($user)
            ->post('/checkout/process', [
                'address_line' => '123 Main St',
                'city' => 'London',
                'phone' => '01234567890',
                'payment_method' => 'Bank Transfer',
                'items' => [1],
            ])
            ->assertSessionHasErrors();
    }
}
```

### Test Coverage Goals

- **Minimum**: 80% code coverage
- **Target**: 90%+ on critical paths (auth, checkout, payments)

## Git Workflow

### Creating a Feature Branch

```bash
# Update main branch
git checkout main
git pull origin main

# Create feature branch
git checkout -b feat/your-feature-name
```

### Branch Naming Convention

- `feat/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation only
- `refactor/` - Code refactoring
- `perf/` - Performance improvements
- `test/` - Adding/updating tests

### Making Commits

```bash
# Stage changes
git add .

# Commit with descriptive message
git commit -m "feat: add product variant support"

# Push to remote
git push origin feat/your-feature-name
```

### Commit Message Format

```
type(scope): subject

body

footer
```

Examples:
- `fix(checkout): prevent race condition with atomic stock locking`
- `feat(admin): add low stock alerts for products`
- `docs(readme): update installation instructions`

## Pull Request Process

### Before Creating a PR

1. **Run tests locally**
   ```bash
   php artisan test
   ```

2. **Format code**
   ```bash
   ./vendor/bin/pint
   ```

3. **Check for N+1 queries** using Laravel Debugbar or application logs

4. **Test manually** on your local environment

### Creating a PR

1. Push your branch to GitHub
2. Create a Pull Request from your branch to `main`
3. Fill out the PR template:

   ```markdown
   ## Description
   Brief description of changes

   ## Type of Change
   - [ ] Bug fix (non-breaking)
   - [ ] New feature (non-breaking)
   - [ ] Breaking change
   - [ ] Documentation update

   ## Testing
   Describe the tests you ran

   ## Checklist
   - [ ] My code follows PSR-12 style guidelines
   - [ ] I have performed self-review
   - [ ] I have added tests for new functionality
   - [ ] All tests pass locally
   - [ ] No N+1 queries introduced
   ```

4. Request review from `@Vithu2008-CS`

### PR Review Process

- At least one approval required before merging
- All CI checks must pass
- All conversations must be resolved

## Reporting Issues

### Before Creating an Issue

1. Check existing issues to avoid duplicates
2. Check the FAQ in README.md
3. Test with latest code from `main` branch

### Creating an Issue

Use clear, descriptive titles and include:

1. **Description**: What is the issue?
2. **Steps to Reproduce**: How to trigger the issue?
3. **Expected Behavior**: What should happen?
4. **Actual Behavior**: What actually happens?
5. **Environment**: PHP version, OS, browser, etc.
6. **Screenshots/Logs**: Attach relevant errors

## Performance Considerations

### Database Queries

- Always use `->with()` for eager loading related models
- Use `->select()` to fetch only needed columns
- Use database indexes for frequently filtered columns
- Avoid queries in loops

### Caching

- Cache product listings: 5 minutes
- Cache search suggestions: 5 minutes
- Invalidate cache when data changes

### Rate Limiting

- API endpoints: 60 requests/min
- Login attempts: 5 attempts/min
- Checkout: 10 requests/min
- File uploads: 20 requests/min

## Security Guidelines

1. **SQL Injection**: Always use parameter binding
   ```php
   // ❌ Bad
   $products = DB::select("SELECT * FROM products WHERE name LIKE '%{$search}%'");

   // ✅ Good
   $products = Product::where('name', 'like', "%{$search}%")->get();
   ```

2. **XSS Prevention**: Escape all user input
   ```php
   {{ $userInput }}           <!-- Auto-escaped in Blade -->
   {!! e($userInput) !!}      <!-- Manual escape if needed -->
   ```

3. **CSRF Protection**: All state-changing requests need CSRF token
   ```blade
   <form method="POST">
       @csrf
       <!-- form fields -->
   </form>
   ```

4. **Authorization**: Always check permissions
   ```php
   $this->authorize('delete', $product);
   ```

## Need Help?

- Check existing issues: https://github.com/Vithu2008-CS/Premier_Shop/issues
- Review documentation: Check ARCHITECTURE.md and SECURITY.md
- Contact: Create a discussion or reach out to the maintainer

Thank you for contributing! 🙌
