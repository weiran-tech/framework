# Weiran Framework 单元测试要求

> 文档版本: v1.0
> 编写日期: 2026-01-27
> 适用项目: Weiran Framework (基于 Laravel 10.x)

---

## 📋 目录

- [1. 项目概述](#1-项目概述)
- [2. 测试架构与标准](#2-测试架构与标准)
- [3. 测试覆盖率要求](#3-测试覆盖率要求)
- [4. 测试编写规范](#4-测试编写规范)
- [5. 测试执行与CI/CD](#5-测试执行与cicd)
- [6. 附录](#6-附录)

---

## 1. 项目概述

### 1.1 项目结构

Weiran Framework 是一个基于 Laravel 10.x 的模块化开发框架,项目采用模块化架构

### 1.2 测试配置说明

项目使用 PHPUnit 10.x 作为测试框架,配置文件位于 `phpunit.xml`:

- **测试隔离**: `processIsolation=true` (每个测试独立进程)
- **严格模式**: `failOnRisky=true`, `failOnWarning=true`
- **环境配置**: 使用 `testing` 环境,Redis缓存

---

## 2. 测试架构与标准

### 2.1 测试基类继承

所有测试类必须继承自框架提供的测试基类:

```php
use Weiran\Framework\Application\TestCase;

class YourTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 初始化逻辑
    }
}
```

### 2.2 测试目录结构规范

每个模块的测试目录必须遵循以下结构:

```
weiran/{module-name}/
├── src/                    # 源代码
│   ├── Action/
│   ├── Classes/
│   ├── Commands/
│   ├── Events/
│   ├── Exceptions/
│   ├── Http/
│   ├── Jobs/
│   ├── Listeners/
│   ├── Models/
│   ├── Services/
│   └── Support/
└── tests/                  # 测试代码 (必须存在)
    ├── Action/             # Action 测试
    ├── Classes/            # 工具类测试
    ├── Commands/           # 命令测试
    ├── Events/             # 事件测试
    ├── Exceptions/         # 异常测试
    ├── Http/               # HTTP/控制器测试
    ├── Jobs/               # 队列任务测试
    ├── Listeners/          # 监听器测试
    ├── Models/             # 模型测试
    ├── Services/           # 服务测试
    ├── Support/            # 辅助函数测试
    ├── TestCase.php        # 模块测试基类 (可选)
    └── files/              # 测试文件 (fixtures)
        ├── *.json
        ├── *.pem
        └── *.txt
```

### 2.3 测试文件命名规范

| 测试类型      | 命名规则                    | 示例                          |
|-----------|-------------------------|-----------------------------|
| 类测试       | `{ClassName}Test.php`   | `RsaCryptTest.php`          |
| Action测试  | `{ActionName}Test.php`  | `UserCreateActionTest.php`  |
| Command测试 | `{CommandName}Test.php` | `CacheClearCommandTest.php` |
| Model测试   | `{ModelName}Test.php`   | `UserModelTest.php`         |
| Service测试 | `{ServiceName}Test.php` | `UserServiceTest.php`       |
| 功能测试      | `{FeatureName}Test.php` | `AuthFeatureTest.php`       |

如果类测试方法超过 600 行则需要进行拆分

| 测试类型  | 命名规则                                          | 示例                        |
|-------|-----------------------------------------------|---------------------------|
| 类方法测试 | `{ClassName}/{ClassName}{MethodName}Test.php` | `RsaCryptDecryptTest.php` |

### 2.4 测试方法命名规范

使用 PHPUnit 标准断言前缀:

```php
public function test{功能描述}(): void
{
    // 测试正常情况
}

public function test{功能描述}With{条件}(): void
{
    // 测试特定条件
}

public function test{功能描述}Throws{异常}When{条件}(): void
{
    // 测试异常情况
}
```

**示例**:

```php
public function testEncrypt(): void
public function testEncryptWithEmptyData(): void
public function testDecryptThrowsExceptionWithInvalidKey(): void
```

---

## 3. 测试覆盖率要求

### 3.1 整体覆盖率目标

| 阶段       | 目标覆盖率 | 
|----------|-------|
| **第一阶段** | 50%   | 
| **第二阶段** | 65%   | 
| **第三阶段** | 75%   | 
| **第四阶段** | 80%+  |

### 3.2 代码覆盖率查看

生成覆盖率报告:

```bash
# 运行测试并生成覆盖率报告
./vendor/bin/phpunit --coverage-html storage/phpunit/coverage-html

# 查看HTML报告
open storage/phpunit/coverage-html/index.html
```

### 3.3 覆盖率排除

以下代码类型可以排除在覆盖率统计之外:

- 路由文件 (`src/Http/Routes/*`)
- 路由服务提供者 (`src/Http/RouteServiceProvider.php`)
- 配置类 (`configurations/*`)
- 视图文件 (`resources/views/*`)
- 数据库迁移文件 (`resources/migrations/*`)

已在 `phpunit.xml` 中配置:

```xml
<source>
    <exclude>
        <directory>modules/**/src/Http/Routes</directory>
        <file>modules/**/src/Http/RouteServiceProvider.php</file>
        <directory>weiran/**/src/Http/Routes</directory>
        <file>weiran/**/src/Http/RouteServiceProvider.php</file>
    </exclude>
</source>
```

---

## 4. 测试编写规范

### 4.1 测试方法结构

每个测试方法应遵循 **AAA 模式** (Arrange-Act-Assert):

```php
public function testUserCreate(): void
{
    // Arrange - 准备测试数据
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ];

    // Act - 执行被测试的操作
    $user = User::create($userData);

    // Assert - 验证结果
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
    $this->assertInstanceOf(User::class, $user);
}
```

### 4.2 测试数据准备

使用 Faker 生成测试数据:

```php
use Poppy\Faker\Factory;

protected function setUp(): void
{
    parent::setUp();
    $this->faker = Factory::create('zh_CN');
}

public function testCreateCategory(): void
{
    $categoryData = [
        'name' => $this->faker->name(),
        'description' => $this->faker->sentence(),
        'sort' => $this->faker->numberBetween(1, 100),
    ];
    // ...
}
```

### 4.3 断言使用

#### 基本断言

```php
// 相等断言
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);  // 严格相等 (类型和值)
$this->assertNotEquals($expected, $actual);

// 布尔断言
$this->assertTrue($condition);
$this->assertFalse($condition);

// 类型断言
$this->assertIsArray($actual);
$this->assertIsString($actual);
$this->assertIsInt($actual);
$this->assertInstanceOf(User::class, $actual);

// 包含断言
$this->assertContains($needle, $haystack);
$this->assertArrayHasKey($key, $array);
$this->assertStringContainsString($needle, $haystack);

// 数量断言
$this->assertCount($expectedCount, $actual);
$this->assertEmpty($actual);
$this->assertNotEmpty($actual);

// 数据库断言
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('users', ['email' => 'notexist@example.com']);
```

#### Laravel 特有断言

```php
// HTTP 响应断言
$response->assertStatus(200);
$response->assertJson(['key' => 'value']);
$response->assertRedirect('/some-route');
$response->assertSessionHas('success');

// 模型断言
$user->assertExists();
$user->assertSoftDeleted();

// 事件断言
Event::assertDispatched(UserCreated::class);
Event::assertNotDispatched(UserDeleted::class);

// 队列断言
Queue::assertPushed(ProcessJob::class);
Queue::assertNothingPushed();

// 门面断言
Mail::assertSent(WelcomeEmail::class);
Notification::assertSentTo($user, PasswordChanged::class);
```

### 4.4 异常测试

```php
public function testUploadThrowsExceptionWithInvalidFile(): void
{
    $this->expectException(InvalidFileException::class);
    $this->expectExceptionMessage('Invalid file type');

    $uploader = new FileUploader();
    $uploader->upload('invalid-file.xyz');
}
```

### 4.5 Mock 和 Stub

#### Mock 外部依赖

```php
public function testSendNotificationWithMockedService(): void
{
    // Mock 外部服务
    $notificationService = $this->mock(NotificationService::class);
    $notificationService->shouldReceive('send')
        ->once()
        ->withArgs(function ($message) {
            return $message->type === 'email';
        })
        ->andReturn(true);

    $user = User::factory()->create();
    $user->notify(new WelcomeMessage());

    // 验证 mock 被调用
    $notificationService->shouldHaveReceived('send')->once();
}
```

#### Mock 数据库

```php
public function testUserRetrievalWithMockedRepository(): void
{
    // 使用 Factory 创建测试数据
    $user = User::factory()->make([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $this->mock(UserRepository::class)
        ->shouldReceive('findById')
        ->with(1)
        ->andReturn($user);

    $userService = new UserService();
    $result = $userService->getUserById(1);

    $this->assertEquals('Test User', $result->name);
}
```

### 4.6 数据库事务

使用数据库事务确保测试隔离:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;  // 每个测试后自动回滚

    public function testUserCreation(): void
    {
        User::factory()->create([
            'email' => 'test@example.com'
        ]);

        $this->assertDatabaseCount('users', 1);
    }
}
```

### 4.7 测试辅助方法

#### 输出变量 (调试用)

```php
$this->outputVariables($data, '测试数据');
```

#### 读取 JSON 测试文件

```php
$config = $this->readJson('system', 'resources/config/test.json');
```

#### 使用 Faker

```php
$faker = $this->faker();
$randomName = $faker->name();
```

### 4.8 测试文件和固定测试数据

将测试文件放在 `tests/files/` 目录:

```
tests/
├── files/
│   ├── test-data.json
│   ├── private.pem
│   ├── public.pem
│   └── sample-text.txt
```

读取测试文件:

```php
$content = file_get_contents(__DIR__ . '/files/test-data.json');
$data = json_decode($content, true);
```

---

## 5. 测试执行与CI/CD

### 5.1 运行测试

#### 运行所有测试

```bash
./vendor/bin/phpunit
```

#### 运行特定测试套件

```bash
# 运行框架测试
./vendor/bin/phpunit --testsuite weiran-core

# 运行系统测试
./vendor/bin/phpunit --testsuite config

# 运行SMS测试
./vendor/bin/phpunit --testsuite weiran-sms
```

#### 运行特定测试文件

```bash
./vendor/bin/phpunit weiran/core/tests/Redis/RdsStrTest.php
```

#### 运行特定测试方法

```bash
./vendor/bin/phpunit --filter testEncrypt
```

#### 运行特定模块的所有测试

```bash
./vendor/bin/phpunit weiran/core/tests
./vendor/bin/phpunit weiran/system/tests
```

### 5.2 生成测试报告

#### 文本输出

```bash
./vendor/bin/phpunit --testdox
```

#### 生成 XML 报告 (CI工具使用)

```bash
./vendor/bin/phpunit --log-junit test-results.xml
```

#### 生成 HTML 覆盖率报告

```bash
./vendor/bin/phpunit --coverage-html coverage-html
```

#### 生成 Clover 覆盖率报告

```bash
./vendor/bin/phpunit --coverage-clover coverage.xml
```

### 5.3 CI/CD 集成

#### GitHub Actions 示例

```yaml
name: Run Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      redis:
        image: redis:latest
        ports:
          - 6379:6379

      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: weiran_test
        ports:
          - 3306:3306

    steps:
      - uses: actions/checkout@v3

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, bcmath, redis, mysql, pdo_mysql

      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Run tests
        run: ./vendor/bin/phpunit --coverage-clover coverage.xml

      - name: Upload coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
```

#### GitLab CI 示例

```yaml
test:
  image: php:8.2

  services:
    - redis:latest
    - mysql:8.0

  variables:
    MYSQL_ROOT_PASSWORD: root
    MYSQL_DATABASE: weiran_test

  before_script:
    - apt-get update && apt-get install -y git unzip
    - pecl install redis
    - docker-php-ext-enable redis
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install --no-interaction

  script:
    - php composer.phar vendor/bin/phpunit --coverage-clover coverage.xml

  artifacts:
    reports:
      coverage_report:
        coverage_format: cobertura
        path: coverage.xml
```

### 5.4 测试性能优化

#### 并行执行测试

```bash
# 使用 Paratest 并行执行
composer require brianium/paratest
./vendor/bin/paratest
```

#### 仅运行失败测试

```bash
./vendor/bin/phpunit --order-by=random
```

#### 停止在第一次失败

```bash
./vendor/bin/phpunit --stop-on-failure
```

---

## 6. 附录

### 6.1 测试优先级定义

| 优先级    | 说明        | 要求               |
|--------|-----------|------------------|
| **P0** | 核心功能,必须覆盖 | 覆盖率 80%+, 优先编写测试 |
| **P1** | 重要功能,重点覆盖 | 覆盖率 70%+         |
| **P2** | 一般功能,基本覆盖 | 覆盖率 60%+         |

### 6.2 测试类型定义

| 测试类型      | 说明       | 示例                            |
|-----------|----------|-------------------------------|
| **单元测试**  | 测试单个类或方法 | `UserServiceTest`             |
| **集成测试**  | 测试多个组件协作 | `UserActionTest`              |
| **功能测试**  | 测试完整业务流程 | `UserRegistrationFeatureTest` |
| **端到端测试** | 测试完整用户场景 | `UserE2ETest`                 |

### 6.3 断言类型参考

| 断言                           | 说明          |
|------------------------------|-------------|
| `assertEquals`               | 值相等         |
| `assertSame`                 | 严格相等 (类型和值) |
| `assertTrue`                 | 条件为真        |
| `assertFalse`                | 条件为假        |
| `assertArrayHasKey`          | 数组包含键       |
| `assertContains`             | 包含元素        |
| `assertCount`                | 数组长度        |
| `assertInstanceOf`           | 实例类型        |
| `assertNull`                 | 值为 null     |
| `assertStringContainsString` | 字符串包含       |
| `assertDatabaseHas`          | 数据库存在记录     |
| `assertDatabaseMissing`      | 数据库不存在记录    |

### 6.4 常用测试 Trait

| Trait             | 说明         |
|-------------------|------------|
| `RefreshDatabase` | 每个测试后重置数据库 |
| `WithFaker`       | 提供Faker实例  |
| `WithMiddleware`  | 测试中间件      |
| `WithEvents`      | 测试事件       |

### 6.5 测试最佳实践

✅ **推荐做法**:

1. 每个测试只测试一个功能点
2. 测试方法命名清晰描述测试内容
3. 使用AAA模式组织测试代码
4. 使用Faker生成真实测试数据
5. 验证正面和负面情况
6. 测试应该独立,不依赖执行顺序
7. Mock外部依赖,隔离被测试代码
8. 保持测试简单和快速
9. 为关键功能编写完整的测试
10. 定期运行测试,确保代码质量

❌ **避免做法**:

1. 测试中包含业务逻辑
2. 过度依赖真实数据库
3. 测试相互依赖
4. 测试包含过多个断言
5. 使用硬编码的测试数据
6. 忽略异常情况
7. 编写重复的测试代码
8. 测试过于复杂难懂
9. 只测试"快乐路径"
10. 跳过测试而不写原因

### 6.6 有用的测试命令速查

```bash
# 运行所有测试
./vendor/bin/phpunit

# 运行特定文件
./vendor/bin/phpunit path/to/Test.php

# 运行特定方法
./vendor/bin/phpunit --filter methodName

# 显示详细输出
./vendor/bin/phpunit --verbose

# 显示测试描述
./vendor/bin/phpunit --testdox

# 停止在第一次失败
./vendor/bin/phpunit --stop-on-failure

# 生成覆盖率HTML
./vendor/bin/phpunit --coverage-html coverage-html

# 只运行修改的文件
./vendor/bin/phpunit --filter @coversAffected

# 显示帮助
./vendor/bin/phpunit --help
```

### 6.7 参考资料

- [PHPUnit 官方文档](https://phpunit.de/documentation.html)
- [Laravel 测试文档](https://laravel.com/docs/10.x/testing)
- [Weiran Framework 文档](https://weiran.tech/weiran-1.x/)
- [测试最佳实践](https://phpunit.de/manual/current/en/appendixes.tests.html)

---

## 文档维护

- **文档维护者**: 开发团队
- **更新频率**: 每季度评估一次
- **反馈渠道**: 项目Issue或代码评审

---

**注**: 本文档为 Weiran Framework 单元测试要求标准,所有开发人员应遵循此规范编写和维护测试。测试是保证代码质量的重要手段,应给予足够重视。
