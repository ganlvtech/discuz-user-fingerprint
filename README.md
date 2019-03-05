# 用户指纹

[![Build Status](https://travis-ci.org/ganlvtech/discuz-user-fingerprint.svg?branch=master)](https://travis-ci.org/ganlvtech/discuz-user-fingerprint)

记录用户的指纹，如果多个账号拥有同一个指纹，很可能这些账号是由同一人使用（即马甲）。

## 原理

本插件目前检测了两套信息。

一个是 Discuz 自带的 `sid` (Session ID)，由于 `sid` 完全是随机生成的，`sid` 相同表示用户在切换账号的时候没有清除 Cookie，这两个账号位于同一台电脑的同一台浏览器上。

另一个是由 [Fingerprintjs2](https://github.com/Valve/fingerprintjs2) 提供的用户指纹信息，同一台电脑的同一个浏览器，即使清除 Cookie 或者进入访客模式、隐身模式都不会改变。不过切换浏览器 UA 会改变这个指纹。这个指纹有可能重复，这个值相同我们不能确信两个账号是马甲。不过基于网上的一些说法，这个指纹区分 90% 以上的用户。

还有其他的一些检测方法，这个插件暂时还未涉及。

## 使用

解压到 `upload/source/plugin/user_fingerprint/`，到 Discuz 后台管理安装、启用插件即可。

## 讨论

我们可以根据 `fingerprint2.js` 返回的数据构造一个巨大的表

每次 fingerprint2.js 提交数据，他会提交一个 fingerprint2

| 用户名 & 用户 id | IP | Cookie (Session ID) | fingerprint2 | User Agent | evercookie

这是一个巨大的多对多表

我们需要从中挖掘出一些数据的规律，
比如某一个用户 id 出现在不同 IP/cookie/fingerprint2 上了（即多人共用账号），
比如某一个 IP/cookie/fingerprint2 上有多个用户（即马甲）。

但是又有一个问题，现在“多人共用账号”这个结果经常出现，原因是每个人有一台电脑、一部手机，另外还可能有一台公司的电脑，甚至现在一个人有两部、三部手机都是很常见的事。
这些电脑、手机有着不一样的 IP、sid、fingerprint2、UA，如何避免误判成了一个问题

还有一个问题，现在的智能手机都是开箱即用的，很多人都用手机自带的浏览器，或者微信、QQ 内置的浏览器，这就造成，智能手机的 UA 只有有限的几种组合，手机 App 有自动更新功能，要么是最新版，要么是系统预装版本

手机型号数量 * 3 种浏览器 * 最新版或者预装版

这样我们单纯地判断 机器码 + IP 非常容易误判。

很多游戏对外挂采用事后追责制，即当时开挂并不会有什么后果，因为他们还不够确定是否是数据错误还是开挂，他们可以一直收集相关信息，直到数据足够表明这个人真的开挂了，那时候再处理才更可信。

我们先收集足够的数据，比如按每条记录 2 KB 计算，我们收集 10 万条数据，大约占 200 MB 的数据库。

然后我们要开始聚类了，两种聚类，一个是把 同一个人的不同账户聚类到一起，另一个是 把同一个账户不同的使用者找出来。

注意：举一个例子，这样的情况我们永远无法判断出这是同一个人使用两个账号。账号 1 只用电脑使用宽带连接访问网站，账号 2 只用手机使用移动数据网络访问互联网，它们拥有不同账号、设备、浏览器、IP，没有任何相同数据，我们没有任何理由判断这两个账号相关。

## 思路

我们需要一个额外的服务器应用，这个应用是用来持续把大量无序数据整理成有规律的数据

sid 相同：一定是同一个人
fingerprint 相同 + IP 相同：非常可能是同一个人
UA 相同 + IP 相同：很可能是同一个人
IP 相同：很可能是同一个人
IP C 段相同：可能是同一个人
fingerprint 相同：可能是同一个人

取出每条记录
    找出与其 sid 相同的不同用户，一定是同一个人  6 分
    找出与其 IP 相同的不同用户，很可能是同一个人  4 分
        如果 fingerprint 也相同，非常可能是同一个人  5 分
    找出与其 IP C 段相同的不同用户，可能是同一个人  2 分
        如果 fingerprint 也相同，很可能是同一个人  3 分
    找出与其 fingerprint 相同的，可能是同一个人  1 分
    找出与其 UA 相同的，可能是同一个人  0.5 分

构造一个表

（UID 小的在前面）
| 用户 A | 用户 B | 相关分 |


## License

    User Fingerprint Discuz! X plugin
    Copyright (C) 2018  Ganlv

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
