# 用户指纹

记录用户的指纹，如果多个账号拥有同一个指纹，很可能这些账号是由同一人使用（即马甲）。

## 原理

本插件目前检测了两套信息。

一个是 Discuz 自带的 `sid` (Session ID)，由于 `sid` 完全是随机生成的，`sid` 相同表示用户在切换账号的时候没有清除 Cookie，这两个账号位于同一台电脑的同一台浏览器上。

另一个是由 [Fingerprintjs2](https://github.com/Valve/fingerprintjs2) 提供的用户指纹信息，同一台电脑的同一个浏览器，即使清除 Cookie 或者进入访客模式、隐身模式都不会改变。不过切换浏览器 UA 会改变这个指纹。这个指纹有可能重复，这个值相同我们不能确信两个账号是马甲。不过基于网上的一些说法，这个指纹区分 90% 以上的用户。

还有其他的一些检测方法，这个插件暂时还未涉及。

## 使用

解压到 `upload/source/plugin/user_fingerprint/`，到 Discuz 后台管理安装、启用插件即可。

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
