The "Kacho" library is designed to cache data "as is" using compiled PHP files; "compiled" means that the data inside them is just declared, similar to just how the JSON works, but in native PHP ;)

In other words you can store various type of data (not just scalars) in PHP files generated by Kacho: that's why you can think of this as the PHP version of JSON.

Here is an example of how to use it:

```
/* Cache current $_SERVER array for one hour */
echo (Kacho::open('/tmp/proba.php')->wrote($_SERVER, 3600)
	? 'Kacho caching is OK :)'
	: 'Kacho caching failed :(';

/* Read the cached $_SERVER array */
echo (Kacho::open('/tmp/proba.php')->read($_SERVER);
```

Make sure that the locations that you provide for the cache files are **writable**, e.g. you can create new files there.
