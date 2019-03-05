# 用户指纹

[![Build Status](https://travis-ci.org/ganlvtech/discuz-user-fingerprint.svg?branch=master)](https://travis-ci.org/ganlvtech/discuz-user-fingerprint)

记录用户的信息，如果多个账号包含相同信息，这些账号很可能是由同一人使用（即马甲）。

## 使用

下载 [Release 版本](https://github.com/ganlvtech/discuz-user-fingerprint/releases)，解压到 `upload/source/plugin/user_fingerprint/`，到 Discuz 后台管理安装、启用插件即可。

## 原理

本插件目前检测了几种信息。

1. Discuz 自带的 `sid` (Session ID)，`sid` 是一个完全是随机生成的六位大小写字母数字组成的字符串，`sid` 相同表示用户在切换账号的时候没有清除 Cookie，这两个账号位于同一台电脑的同一台浏览器上，重复的概率很小，我们认为二者为同一个人。

2. IP

2. 掩码后的 IP，只保留前 24 位的 IP（C 段）。

3. 由 [Fingerprintjs2](https://github.com/Valve/fingerprintjs2) 提供的用户指纹信息，同一台电脑的同一个浏览器，即使清除 Cookie 或者进入访客模式、隐身模式都不会改变。不过切换浏览器 UA 会改变这个指纹。这里有一个问题，现在的智能手机都是开箱即用的，很多人都用手机自带的浏览器，或者微信、QQ 内置的浏览器，这就造成智能手机的 UA 只有有限的几种组合，手机 App 有自动更新功能，要么是最新版，要么是系统预装版本。这个指纹有可能重复，这个值相同我们不能确信两个账号是马甲。不过基于网上的一些说法，这个指纹区分 90% 以上的用户。

4. 简化的指纹，由于部分浏览器的 Canvas Fingerprint 会变化，所以我们去除一部分检测指标（Canvas 和 WebGL），构造一个简化的指纹。

5. User Agent，进一步简化的指纹，只保留一个 User Agent。

每种信息有不同权重，相同项越多，相似度越高，越容易被检测出来。

注意：仅有 UA 相同时不会被记录。

具体算法请参考 `Models/UserRelation.php` 中的 `calcRelation` 函数。您可以自行修改权重。

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
