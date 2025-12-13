*Starting LTI* intends to be a template to help developing IMS LTI tools using the [ceLTIc LTI class library](https://github.com/celtic-project/LTI-PHP).

This fork of Stephen Vickers' [Rating tool](https://github.com/celtic-project/Rating-PHP)
was patched using the ideas of Kyle Tuck's [commits](https://github.com/kylejtuck/Basic-LTI-PHP)
on stripping _Rating_ code, and _platform settings._

Hence, after registering the platform with _admin_ area (see the
[wiki area of Rating-PHP](https://github.com/celtic-project/Rating-PHP/wiki)),
one has to tailor only `config.php`, `index.php`, `libMyTool.php`, and `MyTool.php`
for his own needs.
