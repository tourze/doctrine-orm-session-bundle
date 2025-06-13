# Doctrine ORM Session Bundle 测试计划

## 单元测试

### 1. Entity 测试
- [x] Session 实体创建测试
- [x] Session 数据更新测试
- [x] Session 过期检查测试
- [x] 最后活动时间更新测试
- [x] 默认生命周期测试
- [x] 自定义生命周期测试

### 2. Repository 测试
- [ ] 保存和查找 Session
- [ ] 查找活跃 Session
- [ ] 清理过期 Session
- [ ] 按用户 ID 查找 Session
- [ ] 删除用户所有 Session

### 3. Handler 测试
- [x] open() 方法测试
- [x] close() 方法测试
- [x] read() 读取存在的 Session
- [x] read() 读取不存在的 Session
- [x] write() 创建新 Session
- [x] write() 更新现有 Session
- [x] destroy() 删除 Session
- [x] gc() 垃圾回收测试
- [x] 用户 ID 提取测试

### 4. Command 测试
- [ ] SessionGcCommand 执行测试
- [ ] 无过期 Session 的情况
- [ ] 有过期 Session 的清理
- [ ] 异常处理测试

### 5. CompilerPass 测试
- [ ] 自动配置 session handler
- [ ] 保留用户自定义配置

## 集成测试

### 1. Bundle 注册测试
- [ ] Bundle 正确加载
- [ ] 服务正确注册
- [ ] 参数正确设置

### 2. 功能集成测试
- [ ] Session 读写完整流程
- [ ] 多用户 Session 隔离
- [ ] Session 过期机制
- [ ] 并发访问测试

### 3. Symfony 框架集成
- [ ] 与 Security Bundle 集成
- [ ] 与 Controller Session 使用
- [ ] 环境变量配置

## 性能测试

### 1. 负载测试
- [ ] 高并发 Session 创建
- [ ] 大量 Session 数据存储
- [ ] 垃圾回收性能

### 2. 压力测试
- [ ] 数据库连接池测试
- [ ] 内存使用监控
- [ ] 查询性能分析

## 安全测试

### 1. Session 固定攻击防护
- [ ] Session ID 重新生成
- [ ] IP 地址验证

### 2. 数据安全
- [ ] Session 数据加密（如需要）
- [ ] SQL 注入防护
- [ ] XSS 防护

## 兼容性测试

### 1. PHP 版本
- [ ] PHP 8.1
- [ ] PHP 8.2
- [ ] PHP 8.3

### 2. Symfony 版本
- [ ] Symfony 6.4
- [ ] Symfony 7.0

### 3. Doctrine 版本
- [ ] Doctrine ORM 2.x
- [ ] Doctrine ORM 3.x

## 部署测试

### 1. 安装测试
- [ ] Composer 安装
- [ ] 自动配置验证
- [ ] 数据库迁移

### 2. 升级测试
- [ ] 版本升级兼容性
- [ ] 数据迁移
- [ ] 配置迁移