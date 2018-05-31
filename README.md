# 
正方教务系统成绩查询手机版，结合Weui，自动识别验证码，通过模拟请求抓取教务系统成绩信息

正方教务系统成绩查询 2.0 版本

请认真查看下面的说明：

版本要求：PHP版本建议在5.6以上，
        务必安装curl扩展，否则无法使用curl等方法，
        ps：5.6版本以下默认关闭失败验证码，如验证码显示不了或者识别失败，请更换php版本试试

使用方法：修改code/captcha.php文件的IP地址即可

更新内容

1.重构目录及源码等规范，方便扩展与阅读;

2.新增自动识别验证码，准确率在87%;

3.新增日期选项自动填充

4.新增通过Cookie保存用户名，有效期30天;

项目演示地址：http://cj.kbteam.cn/

项目截图

![image](https://github.com/kbdxbt/cj/raw/master/image/1.png)
![image](https://github.com/kbdxbt/cj/raw/master/image/2.png)
![image](https://github.com/kbdxbt/cj/raw/master/image/3.png)

