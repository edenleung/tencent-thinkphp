# 腾讯云 ThinkPHP Serverless Component

简体中文 | [English](./README.en.md)

## 简介

腾讯云 [ThinkPHP](https://github.com/top-think/think) Serverless Component, 支持 Restful API 服务的部署。

## 目录

0. [准备](#0-准备)
1. [安装](#1-安装)
1. [配置](#2-配置)
1. [部署](#3-部署)
1. [移除](#4-移除)

### 0. 准备

#### 初始化 ThinkPHP 项目

在使用此组件之前，你需要先自己初始化一个 `ThinkPHP` 项目

```shell
$ composer create-project topthink/think serverless-thinkphp
```

> 注意：ThinkPHP 使用 Composer 管理依赖的，所以你需要先自行安装 Composer，请参考 [官方安装文档](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)

### 1. 安装

通过 npm 全局安装 [serverless cli](https://github.com/serverless/serverless)

```shell
$ npm install -g serverless
```

### 2. 配置

在项目根目录，创建 `serverless.yml` 文件，在其中进行如下配置

```shell
$ touch serverless.yml
```

```yml
# serverless.yml

MyThinkPHP:
  component: '@serverless/tencent-thinkphp'
  inputs:
    region: ap-guangzhou
    functionName: thinkphp-function
    code: ./
    functionConf:
      timeout: 10
      memorySize: 128
      environment:
        variables:
          TEST: abc
      vpcConfig:
        subnetId: ''
        vpcId: ''
    apigatewayConf:
      protocols:
        - https
      environment: release
```

- [更多配置](https://github.com/serverless-components/tencent-thinkphp/tree/master/docs/configure.md)

### 3. 部署

> 注意：**在部署前，你需要先清理本地运行的配置缓存，执行 `php think clear` 即可。**

如您的账号未 [登陆](https://cloud.tencent.com/login) 或 [注册](https://cloud.tencent.com/register) 腾讯云，您可以直接通过 `微信` 扫描命令行中的二维码进行授权登陆和注册。

通过 `sls` 命令进行部署，并可以添加 `--debug` 参数查看部署过程中的信息

```shell
$ sls --debug

  DEBUG ─ Resolving the template's static variables.
  DEBUG ─ Collecting components from the template.
  DEBUG ─ Downloading any NPM components found in the template.
  DEBUG ─ Analyzing the template's components dependencies.
  DEBUG ─ Creating the template's components graph.
  DEBUG ─ Syncing template state.
  DEBUG ─ Executing the template's components graph.
  DEBUG ─ Compressing function thinkphp-function file to /Users/yugasun/Desktop/Develop/serverless/tencent-thinkphp/example/.serverless/thinkphp-function.zip.
  DEBUG ─ Compressed function thinkphp-function file successful
  DEBUG ─ Uploading service package to cos[sls-cloudfunction-ap-guangzhou-code]. sls-cloudfunction-default-thinkphp-function-1583729311.zip
  DEBUG ─ Uploaded package successful /Users/yugasun/Desktop/Develop/serverless/tencent-thinkphp/example/.serverless/thinkphp-function.zip
  DEBUG ─ Creating function thinkphp-function

  DEBUG ─ Created function thinkphp-function successful
  DEBUG ─ Setting tags for function thinkphp-function
  DEBUG ─ Creating trigger for function thinkphp-function
  DEBUG ─ Deployed function thinkphp-function successful
  DEBUG ─ Starting API-Gateway deployment with name MyThinkPHP.TencentApiGateway in the ap-guangzhou region
  DEBUG ─ Service with ID service-jgey4hi8 created.
  DEBUG ─ API with id api-jeilvfr6 created.
  DEBUG ─ Deploying service with id service-jgey4hi8.
  DEBUG ─ Deployment successful for the api named MyThinkPHP.TencentApiGateway in the ap-guangzhou region.

  MyThinkPHP:
    region:              ap-guangzhou
    functionName:        thinkphp-function
    apiGatewayServiceId: service-jgey4hi8
    url:                 https://service-jgey4hi8-1251556596.gz.apigw.tencentcs.com/release/

  22s › MyThinkPHP › done
```

> 注意: `sls` 是 `serverless` 命令的简写。

### 4. 移除

通过以下命令移除部署的 API 网关

```shell
$ sls remove --debug

  DEBUG ─ Flushing template state and removing all components.
  DEBUG ─ Removing function
  DEBUG ─ Request id
  DEBUG ─ Removed function thinkphp-function successful
  DEBUG ─ Removing any previously deployed API. api-jeilvfr6
  DEBUG ─ Removing any previously deployed service. service-jgey4hi8

  8s › MyThinkPHP › done
```

### 账号配置（可选）

当前默认支持 CLI 扫描二维码登录，如您希望配置持久的环境变量/秘钥信息，也可以本地创建 `.env` 文件

在 `.env` 文件中配置腾讯云的 SecretId 和 SecretKey 信息并保存

如果没有腾讯云账号，可以在此 [注册新账号](https://cloud.tencent.com/register)。

如果已有腾讯云账号，可以在 [API 密钥管理](https://console.cloud.tencent.com/cam/capi) 中获取 `SecretId` 和`SecretKey`.

```text
# .env
TENCENT_SECRET_ID=123
TENCENT_SECRET_KEY=123
```

### 更多组件

可以在 [Serverless Components](https://github.com/serverless/components) repo 中查询更多组件的信息。
