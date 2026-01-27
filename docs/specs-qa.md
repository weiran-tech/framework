# 项目规范

## 框架规范

**蔚然定义**
- 该框架基于 Laravel 10 + php 8.2 进行开发
- 该框架目录位置是在 `/weiran` 目录下或者以包的形式安装在 `/vendor/weiran` 目录下
- 又名 weiran 框架

**禁止扫描**

- `/public` 目录是资源和入口文件, 不要读取
- `/vendor` 是包文件, 非特定要求不读取
- `/storage` 是系统缓存目录, 非特定要求不读取

**配置文件**

- 配置文件在 `/config` 目录下
- 自定义配置文件 `weiran.php`, `module.php` 的数组深度不超过二级, 二级的 value 不可以是 kv 数组, 可以是 `array[]`
    - 一级: `'key' => value`
    - 二级: `'key' => ['subkey' => value]`


## 模块定义

**结构**

- 所有的代码模块均放置在 `modules`, `weiran` 目录下
- `@deprecated` 的类/方法不参与检查

**模块结构**

- `weiran/framwork` 目录, `weiran/faker` 目录, `weiran/ext-*` 不用遵循以下目录结构
- 以下为推荐的标准模块结构（`*` 为可选目录或文件）:

```
├── configurations           # 配置目录, 目录必须存在
│   ├── permissions.yaml*    # 权限定义
│   ├── menus.yaml*          # 菜单定义
│   ├── services.yaml*       # 服务定义文件
│   ├── hooks.yaml*          # 服务钩子定义文件
├── resources                # 资源
│   ├── config*              # 发布的配置文件
│   ├── lang*                # 多语言支持包的位置
│   │   └── zh*    
│   │      ├── seo.php*      # SEO 文件
│   │      └── util.php*     # Util 文件
│   └── views*               # 视图文件
│       ├── backend*         # 视图 - 后台
│       └── ...              # 视图 - 其他内容
├── src    
│   ├── Action               # 业务逻辑文件
│   ├── Classes*             # 基础类, 非业务逻辑类文件
│   │   └── {Module}Def.php* # 模块定义文件
│   ├── Commands             # 命令行目录
│   ├── Events               # 事件目录
│   ├── Http                 # 路由和中间件
│   │   ├── ...              # 控制器 / Validation / Swagger 文件定义
│   │   ├── Middlewares      # 中间件目录
│   │   └── Routes           # 路由定义目录, 存在 RouteServiceProvider 时候必须存在
│   ├── Listeners            # 事件的监听器, 按照事件名称进行目录组织
│   ├── Models               # 模型
│   │   ├── Policies         # 模型 - 策略目录
│   │   └── Resources        # 模型 - 资源目录
│   │── Notifications*       # 通知
│   └── Jobs*                # 队列文件
└── tests                    # 单元测试
```

**命名空间**

- 源码在根目录 `composer.json` 的 `autoload.psr-4` 进行定义, 例如 `modules/user/src` 目录下的模块命名空间为 `User\`
- 单元测试的加载在 `composer.json` 的 `autoload-dev.psr-4` 进行定义, 例如 `modules/user/tests` 目录下的单元测试命名空间为
  `User\Tests\`

**src/Events 目录**

- 文件必须以 `Event` 后缀结尾 (如 `UserCreatedEvent.php`)

**src/Commands 目录**

- 文件必须以 `Command` 结尾

**src/Listeners 目录**

- 文件必须以 `Listener` 后缀结尾 (如 `SendEmailListener.php`)

**src/Models 目录**

- 不包含单元测试目录
- 文件名称一般是表名的 `CamelCase` 格式
- **Models/Policies 目录**: 文件必须以 `Policy` 后缀结尾 (如 `UserPolicy.php`)
- **Models/Resources 目录**: 文件必须以 `Resource` 后缀结尾 (如 `UserResource.php`)

**src/Http 目录**

- 文件以 `Controller` 结尾为控制器文件
- 文件以 `Request` 结尾为Form 验证文件 / Request 请求校验 / OpenApi 参数文件
- 文件以 `List` 结尾为列表渲染文件
- 文件以 `Form` 结尾为表单渲染文件
- 文件以 `Body`, `Item` 结尾为 OpenApi 请求体 Response 语法文件

**Http/Routes 目录**

- 存放路由定义文件
- 路由完整地址的生成是由多个部分追加
    - **前缀** : `modules/{module}/src/Http/RouteServiceProvider.php` 文件中的 `Route:group()` 存在的 `prefix` 参数
    - **后缀** : `Route:group()` 加载的指定的路由文件的路由定义清单
- 路由定义文件禁止使用 `namespace` 参数
- 路由可以不用命名

**src/Action 目录**

- 存放业务逻辑文件
- **modules** 目录下的文件必须以 `Act` 作为前缀
- **weiran** 目录下的文件不做限制

## 编码说明

**校验范围**
- 针对的目录是 `modules/{module}/src` 和 `modules/{module}/tests` 目录
- 针对的文件是 `*.php` 文件
- 对于继承自三方包的类, 不进行校验

**类命名**

- 使用 PascalCase (大驼峰) 命名法
- 类名应该是名词
- 接口以 `Interface` 结尾 (可选)
- 抽象类以 `Abstract` 开头 (可选)
- Trait 类以 `Trait` 结尾 (可选)
- 示例: `UserController`, `AuthService`, `LoggerInterface`

**变量和方法命名**

- 使用 camelCase (小驼峰) 命名法
- 属性: `$userName`, `$isActive`
- 方法: `getUserName()`, `setActive()`
- 布尔变量/获取布尔变量的方法以 `is`、`has`、`can` 等前缀开头
- 示例: `$isActive`, `hasPermission()`, `canDelete()`

**常量命名**

- 使用 UPPER_CASE 命名法
- 类常量同类型必须要进行分组
- 示例: `MAX_RETRY_COUNT`, `DEFAULT_TIMEOUT`

**函数命名**

- 同变量和方法命名，使用 camelCase
- 示例: `sendEmail()`, `validateUser()`

**类注释**

- 每个类必须有文档注释说明其用途

**方法注释**

- 每个 `modules/{module}/src/Action` 方法必须有文档注释说明其用途 (包括 `modules` 和 `weiran` 目录)

**常量注释**

- 类常量同类型必须要进行分组

**路由命名规范**

- 路由可以不命名
- 路由若命名, 名称必须符合格式: `{module}:{type}.{group}.{action}`
    - `{module}` 必须存在
    - `{type}` 命名为 `api_*` 或 `backend`, `web` 限制词
- 路由的命名在 `weiran` 目录下的 `{module}` 命名为 `weiran-{}`
- 路由的命名在 `modules` 目录下的 `{module}` 命名为 `{module}`, 或者 `{module}-` 前缀

- **接口规范**

- `Http/**/Api*`, `Http/Api*` 文件下的公共方法都必须要编写接口文档
- 接口必须要符合格式 : `/api/{type}/{module}/{version}/{group}/{action}`, 例如 : `/api/web/system/v1/core/info`
- OpenApi 文档编写使用 [OpenApi](https://github.com/zircote/swagger-php) 规范
- 使用 `php artisan core:doc api` 生成 OpenApi 文档

**严格模式**

- 项目指定的目录必须包含 `declare(strict_types = 1)` 来声明严格模式
- 包含目录 `modules/{module}/src`
- 排除目录 `modules/{module}/src/Http/Routes`

**Deprecated**

- weiran 框架中使用 `@deprecated` 标签的方法，表示该方法已弃用
- 对于已弃用的方法，不推荐在模块中使用或者引用
- 对于 php 8.2 中不推荐的语法, 或者新增的语法糖, 可以替代当前写法的, 需要予以改正

**Forbidden**

- 项目 `src`, `tests` 目录下的文件, 不得包含 `dd`, `dump`, `var_dump`, `print_r` 的调试函数

## 国际化

**验证规则**

- 所有验证规则必须有中文翻译，位于 `/resources/lang/zh/validation.php`
- `/resources/lang/zh/validation.php` 和 `/resources/lang/en/validation.php` 中的定义数量必须一致

**SEO 标题**

- 每个路由必须有对应的 SEO 翻译 key
- 路由 `{module}:{type}.{group}.{action}` 的 key 是 `{type}_{group}_{action}`

**util 翻译**

- Policy 方法需要对应的翻译键: `policy.{model}.{method}`
- Model 类需要对应的翻译键: `classes.models.{model_snake_case}`

