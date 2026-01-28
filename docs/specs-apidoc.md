# Weiran System API 接口文档规范

本文档基于 `@weiran/system/src/Http/` 目录下的控制器 OpenAPI 注解编写，旨在提供统一的接口文档规范，方便 AI 对其他项目进行验证。

## 目录

1. [文档元信息](#文档元信息)
2. [接口定义规范](#接口定义规范)
3. [请求体规范](#请求体规范)
4. [响应体规范](#响应体规范)
5. [参数规范](#参数规范)
6. [完整示例](#完整示例)

---

## 文档元信息

每个接口文档应包含以下基础信息：

| 字段       | 说明           | 示例                               |
|----------|--------------|----------------------------------|
| **接口路径** | 完整的 API 路径   | `/api/web/system/v1/auth/login`  |
| **请求方法** | HTTP 方法      | `POST`                           |
| **接口名称** | 简洁的功能描述      | `登录`                             |
| **接口描述** | 详细的接口说明（可选）  | `用户账号密码或验证码登录`                   |
| **认证方式** | 需要的中间件认证     | `api-sign`, `sys-jwt`, `api-sso` |
| **所属标签** | OpenAPI 标签分组 | `System`                         |

---

## 接口定义规范

### 基础结构

接口定义使用 OpenAPI (Swagger) PHP Attributes 注解：

```php
#[OA\{METHOD}(
    path: '/api/web/system/v1/xxx',
    summary: '接口名称',
    description: '接口详细描述(可选)',
    tags: ['标签名'],
    // parameters 或 requestBody
    // responses
)]
```

METHOD : GET | POST | PUT | DELETE | OPTIONS

### 规范要求

1. **path**: 完整的 API 路径，必须与路由文件定义一致
2. **summary**: 简洁的中文接口名称，不超过 20 字
3. **description**: 可选，提供更详细的说明
4. **tags**: 接口分组标签，使用大驼峰命名
5. **请求参数**: GET 使用 `parameters`，POST/PUT 使用 `requestBody`
6. **响应定义**: `responses` 数组必须包含至少一个 200 响应

---

## 请求体规范

### 使用独立的 Request 类

推荐为每个接口创建独立的 Request 类，使用 `#[OA\Schema]` 注解：

```php
#[OA\Schema(
    required: ['passport'],
    properties: [
        new OA\Property(
            property: 'passport',
            description: '通行证',
            type: 'string',
        ),
        new OA\Property(
            property: 'password',
            description: '密码',
            type: 'string',
        ),
    ]
)]
class AuthLoginRequest extends Request
{
    // ...
}
```

### Request 类规范

| 字段            | 说明      | 示例                                                |
|---------------|---------|---------------------------------------------------|
| `required`    | 必填字段数组  | `['passport']`                                    |
| `property`    | 字段名称    | `passport`                                        |
| `description` | 字段中文描述  | `通行证`                                             |
| `type`        | 字段类型    | `string`, `integer`, `boolean`, `array`, `object` |
| `enum`        | 枚举值（可选） | `['web', 'backend']`                              |

### 在接口中引用

```php
#[OA\Post(
    path: '/api/web/system/v1/auth/login',
    summary: '登录',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: AuthLoginRequest::class)
    ),
    tags: ['System'],
    responses: [...]
)]
```

---

## 响应体规范

### 使用独立的 Response 类

推荐为每个响应创建独立的 ResponseBody 类，继承自 `BaseResponseBody`：

```php
#[OA\Schema(
    description: '登录成功',
)]
class AuthLoginResponseBody extends BaseResponseBody
{
    #[OA\Property(
        description: '登录成功返回的token信息',
        properties: [
            new OA\Property(property: 'token', description: 'Token', type: 'string'),
            new OA\Property(property: 'type', description: '类型', type: 'string'),
            new OA\Property(property: 'is_register', description: '是否是注册', type: 'string'),
        ],
        type: 'object'
    )]
    public object $data;
}
```

### BaseResponseBody 结构

基础响应体包含以下字段：

```json
{
  "code": 200,
  "message": "操作成功",
  "data": {}
}
```

### Response 类规范

| 字段            | 说明           | 示例       |
|---------------|--------------|----------|
| `description` | 响应的中文描述      | `登录成功`   |
| `properties`  | data 对象的属性定义 | 见上方示例    |
| `type`        | data 字段类型    | `object` |

### 在接口中引用

```php
responses: [
    new OA\Response(
        response: 200,
        description: '登录成功',
        content: new OA\JsonContent(ref: AuthLoginResponseBody::class)
    ),
]
```

---

## 参数规范

### Query/Path Parameters

用于 GET 请求或 URL 路径参数：

```php
parameters: [
    new OA\Parameter(
        name: 'token',
        description: 'Token',
        in: 'query',      // 'query' | 'path' | 'header'
        required: true,
        schema: new OA\Schema(type: 'string')
    ),
]
```

### 参数规范

| 字段            | 说明     | 示例                              |
|---------------|--------|---------------------------------|
| `name`        | 参数名称   | `token`                         |
| `description` | 参数中文描述 | `Token`                         |
| `in`          | 参数位置   | `query`, `path`, `header`       |
| `required`    | 是否必填   | `true`, `false`                 |
| `schema`      | 参数类型定义 | `new OA\Schema(type: 'string')` |

### 参数类型

| Type      | 说明  | 示例值                |
|-----------|-----|--------------------|
| `string`  | 字符串 | `"hello"`          |
| `integer` | 整数  | `123`              |
| `boolean` | 布尔值 | `true`             |
| `array`   | 数组  | `[1, 2, 3]`        |
| `object`  | 对象  | `{"key": "value"}` |

---

## 完整示例

### 示例 1：POST 请求（登录）

```php
/**
 * 认证控制器
 */
class AuthController extends JwtApiController
{
    #[OA\Post(
        path: '/api/web/system/v1/auth/login',
        summary: '登录',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: AuthLoginRequest::class)
        ),
        tags: ['System'],
        responses: [
            new OA\Response(
                response: 200,
                description: '登录成功',
                content: new OA\JsonContent(ref: AuthLoginResponseBody::class)
            ),
        ]
    )]
    public function login(Request $req): JsonResponse
    {
        // 实现
    }
}
```

**请求体（AuthLoginRequest）:**

```php
#[OA\Schema(
    required: ['passport'],
    properties: [
        new OA\Property(
            property: 'passport',
            description: '通行证',
            type: 'string',
        ),
        new OA\Property(
            property: 'password',
            description: '密码',
            type: 'string',
        ),
        new OA\Property(
            property: 'captcha',
            description: '验证码',
            type: 'string',
        ),
        new OA\Property(
            property: 'device_id',
            description: '设备ID',
            type: 'string',
        ),
        new OA\Property(
            property: 'guard',
            description: '登录类型[web|用户(默认);backend|后台;]',
            type: 'string',
            enum: [PamAccount::GUARD_WEB, PamAccount::GUARD_BACKEND],
        ),
    ]
)]
class AuthLoginRequest extends Request
{
    // ...
}
```

**响应体（AuthLoginResponseBody）:**

```php
#[OA\Schema(
    description: '登录成功',
)]
class AuthLoginResponseBody extends BaseResponseBody
{
    #[OA\Property(
        description: '登录成功返回的token信息',
        properties: [
            new OA\Property(property: 'token', description: 'Token', type: 'string'),
            new OA\Property(property: 'type', description: '类型', type: 'string'),
            new OA\Property(property: 'is_register', description: '是否是注册', type: 'string'),
        ],
        type: 'object'
    )]
    public object $data;
}
```

### 示例 2：GET 请求（检测 Token）

```php
#[OA\Get(
    path: '/api/web/system/v1/auth/access',
    description: '检测 Token',
    summary: '检测 Token',
    tags: ['System'],
    parameters: [
        new OA\Parameter(
            name: 'token',
            description: 'Token',
            in: 'query',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: '获取成功',
            content: new OA\JsonContent(ref: AuthAccessResponseBody::class)
        ),
    ]
)]
public function access(): JsonResponse
{
    // 实现
}
```

**响应体（AuthAccessResponseBody）:**

```php
#[OA\Schema(
    description: '获取成功',
)]
class AuthAccessResponseBody extends BaseResponseBody
{
    #[OA\Property(
        description: '用户信息',
        properties: [
            new OA\Property(property: 'id', description: 'ID', type: 'integer'),
            new OA\Property(property: 'username', description: '用户名', type: 'string'),
            new OA\Property(property: 'mobile', description: '手机号', type: 'string'),
            new OA\Property(property: 'email', description: '邮箱', type: 'string'),
            new OA\Property(property: 'type', description: '类型', type: 'string'),
            new OA\Property(property: 'is_enable', description: '是否启用(Y|N)', type: 'string'),
        ],
        type: 'object'
    )]
    public object $data;
}
```

---

## 验证清单

AI 在验证其他项目时，应检查以下内容：

- [ ] 所有控制器方法都有对应的 `#[OA\Get|Post|...]` 注解
- [ ] `path` 与路由文件定义一致
- [ ] `summary` 存在且为简洁的中文描述
- [ ] `tags` 使用正确的分组名称
- [ ] POST/PUT 使用独立的 Request 类并引用
- [ ] Request 类有 `#[OA\Schema]` 注解
- [ ] Request 类中 `required` 和 `properties` 定义完整
- [ ] 响应使用独立的 ResponseBody 类
- [ ] ResponseBody 继承自 `BaseResponseBody`
- [ ] 所有 `description` 为中文
- [ ] `type` 使用正确的类型值
- [ ] 枚举值使用 `enum` 属性定义

---

## 命名规范

| 类型                  | 规范                 | 示例                              |
|---------------------|--------------------|---------------------------------|
| **Request 类名**      | `{功能}Request`      | `AuthLoginRequest`              |
| **ResponseBody 类名** | `{功能}ResponseBody` | `AuthLoginResponseBody`         |
| **Controller 类名**   | `{模块}Controller`   | `AuthController`                |
| **接口路径**            | 小写 + 下划线           | `/api/web/system/v1/auth/login` |
| **字段名称**            | 小写 + 下划线           | `device_id`                     |
| **标签名称**            | 大驼峰                | `System`                        |
