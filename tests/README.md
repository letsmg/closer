# Test Suite Documentation

This document describes the comprehensive test suite created for the Closer application database interactions.

## Overview

The test suite covers all database interactions including:
- Unit tests for Models
- Feature tests for Controllers
- Migration and Seeder tests
- Database transaction tests
- Factory definitions for test data

## Test Structure

### Unit Tests (`tests/Unit/`)

#### UserTest.php
Tests the User model functionality:
- User creation with required fields
- User level methods (Free, Plus, Premium, Admin)
- User access permissions and limits
- JWT token methods
- Scopes and relationships
- Data casting and validation

#### ProfileTest.php
Tests the Profile model functionality:
- Profile creation and relationships
- Location relationships (Country, State, City)
- Many-to-many relationships (Hobbies, Languages)
- Visibility scopes and methods
- Profile completion percentage
- Access control methods

#### MessageTest.php
Tests the Message model functionality:
- Message creation and relationships
- Read/unread status management
- Sender and recipient relationships
- Data validation and casting

#### LikeModelTest.php
Tests the LikeModel functionality:
- Like and dislike creation
- User relationships
- Unique constraints
- Boolean casting

#### HobbyTest.php
Tests the Hobby model functionality:
- Hobby creation and relationships
- Profile associations
- Search functionality
- Active/inactive status

### Feature Tests (`tests/Feature/`)

#### Auth/AuthControllerTest.php
Tests authentication endpoints:
- User registration with validation
- Login with various scenarios
- Logout functionality
- Admin user banning
- Email blocking verification

#### Profile/ProfileControllerTest.php
Tests profile management endpoints:
- Profile viewing and updating
- File upload handling
- Validation and permissions
- Location and hobby management

#### Message/MessageControllerTest.php
Tests messaging endpoints:
- Message sending and receiving
- Read status management
- Match validation
- Permission checks

#### Like/LikeControllerTest.php
Tests like/dislike endpoints:
- Like and dislike creation
- Mutual like matching
- Permission validation
- Like history viewing

#### MigrationTest.php
Tests database migrations:
- Table structure validation
- Column existence and types
- Foreign key constraints
- Index presence

#### SeederTest.php
Tests database seeders:
- Data creation and relationships
- Uniqueness constraints
- Active flag settings
- Idempotent operations

#### DatabaseTransactionTest.php
Tests database transactions:
- Atomic operations
- Rollback scenarios
- Concurrency handling
- Foreign key constraints

## Factories (`database/factories/`)

### Available Factories

1. **UserFactory** - User model test data
2. **ProfileFactory** - Profile model test data
3. **MessageFactory** - Message model test data
4. **LikeModelFactory** - Like model test data
5. **HobbyFactory** - Hobby model test data
6. **CountryFactory** - Country model test data
7. **StateFactory** - State model test data
8. **CityFactory** - City model test data
9. **LanguageFactory** - Language model test data
10. **UserMatchFactory** - User match test data
11. **ProfilePhotoFactory** - Profile photo test data

### Factory Features

Each factory includes:
- Basic definition with realistic fake data
- State methods for specific scenarios
- Relationship creation methods
- Localization support (Brazil-focused data)
- Data validation helpers

## Test Configuration

### TestCase Base Class

The base TestCase class provides:

#### Helper Methods
- `createAuthenticatedUser()` - Creates user with token
- `createAdminUser()` - Creates admin user
- `createPremiumUser()` - Creates premium user
- `createPlusUser()` - Creates plus user
- `createFreeUser()` - Creates free user

#### Request Helpers
- `authenticatedRequest()` - Makes authenticated API calls
- `adminRequest()` - Makes admin-level API calls

#### Assertion Helpers
- `assertTableStructure()` - Validates table schema
- `assertValidationErrors()` - Checks validation failures
- `assertUnauthorized()` - Checks 401 responses
- `assertForbidden()` - Checks 403 responses
- `assertNotFound()` - Checks 404 responses

#### Data Creation Helpers
- `createTestUserData()` - Creates complete user profile
- `createMatch()` - Creates user match
- `createConversation()` - Creates message conversation

### Database Configuration

Tests use SQLite in-memory database for isolation:
- Fast execution
- Complete isolation between tests
- Automatic cleanup

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test File
```bash
php artisan test tests/Unit/UserTest.php
```

### Specific Test Method
```bash
php artisan test --filter test_user_can_be_created_with_required_fields
```

### With Coverage
```bash
php artisan test --coverage
```

## Test Categories

### 1. Unit Tests
- **Purpose**: Test individual model methods and relationships
- **Isolation**: Each test runs in isolation
- **Database**: Uses in-memory SQLite
- **Speed**: Fast execution

### 2. Feature Tests
- **Purpose**: Test API endpoints and business logic
- **Integration**: Tests multiple components working together
- **HTTP**: Simulates real HTTP requests
- **Validation**: Tests request/response cycles

### 3. Database Tests
- **Purpose**: Test database structure and data integrity
- **Migrations**: Validates schema changes
- **Seeders**: Tests data population
- **Transactions**: Tests data consistency

## Best Practices

### Test Organization
- Group related tests in same file
- Use descriptive test method names
- Follow Arrange-Act-Assert pattern
- Keep tests focused and independent

### Data Management
- Use factories for test data
- Clean up after each test
- Use RefreshDatabase trait
- Avoid hard-coded test data

### Assertions
- Use specific assertion methods
- Test both success and failure cases
- Validate response structure
- Check database state changes

### Performance
- Use in-memory database
- Minimize unnecessary data creation
- Use appropriate test data amounts
- Run tests in parallel when possible

## Coverage Areas

### ✅ Covered
- All model relationships
- API endpoint authentication
- Data validation rules
- Database constraints
- Business logic flows
- Error handling scenarios

### 🔄 Partial Coverage
- File upload edge cases
- Complex query optimizations
- Performance benchmarks
- Load testing scenarios

### ❌ Not Covered
- Frontend integration
- Third-party API integrations
- Email sending functionality
- Real-time features (WebSocket)
- Performance under load

## Maintenance

### Adding New Tests
1. Create appropriate test file
2. Use existing helper methods
3. Follow naming conventions
4. Add factory methods if needed
5. Update documentation

### Updating Existing Tests
1. Run tests before changes
2. Update affected test cases
3. Verify factory compatibility
4. Update documentation
5. Run full test suite

### Test Data Management
1. Keep factories up to date
2. Add new state methods as needed
3. Maintain realistic test data
4. Clean up unused factories
5. Review data relationships

## Troubleshooting

### Common Issues
1. **Factory not found** - Check namespace and file location
2. **Database not migrated** - Run migrations in setUp()
3. **Authentication failures** - Check token generation
4. **Missing relationships** - Verify model definitions

### Debug Tips
1. Use `dd()` for debugging test data
2. Check database state in tests
3. Verify request/response data
4. Use specific assertions
5. Run tests individually

## Future Enhancements

### Planned Improvements
1. Add performance benchmarks
2. Implement parallel test execution
3. Add integration test environment
4. Create test data visualization
5. Add automated test reporting

### Test Types to Add
1. Load testing scenarios
2. Security testing
3. Accessibility testing
4. Cross-browser testing
5. Mobile API testing

This comprehensive test suite ensures data integrity, validates business logic, and provides confidence in database interactions throughout the Closer application.
