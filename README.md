# K-Laravel12

#### 使用

##### 环境要求

- PHP >= 8.2
- Composer
- MySQL 8.0 / PostgreSQL 16.9
- Redis
- 注意，使用 PostgreSQL 需要安装 php 扩展 pdo_pgsql、pgsql

#### 安装依赖

- ``composer install``

#### 配置

- ``cp .env.example .env``
- ``php artisan key:generate``

#### 运行迁移

- ``php artisan migrate``

#### 运行填充

- ``php artisan db:seed``

#### 本地开发环境优化

- laravel-ide-helper包
    - ``php artisan ide-helper:generate``
    - ``php artisan ide-helper:models``
    - ``php artisan ide-helper:meta``

#### Sanctum 命令

- 令牌过期
    - ``sanctum:prune-expired`` 删除所有已过期至少 24 小时的令牌数据库记录
