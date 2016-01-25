# Fruit Framework

This package is the core of Fruit Framework.

Fruit is still under development, not usable now.

## What is Fruit

```
A fruit is the seed-bearing structure in angiosperms formed from the ovary after flowering. (Wikipedia)
```

Fruit framework tries to restructure the configuration and helper modules into raw executable PHP code.

## Explain it

Basically, Fruit converts dynamic call into static call.

The work flow when a request comes in in most web frameworks is something like

1. Execute a special PHP script (entry point), prepare basic data like auto-loading information.
2. Load configuration. Mostly by passing config file to a helper class.
3. Parse the configuration, load needed modules (like router) and pass related config items to it.
4. Find correct controller using router, and execute it.

For knowing which module is "needed", frameworks must prepare a set of data, then parse it to dynamically load the modules.

Fruit works differently.

It provides a command line tool to "compile" your configuration file into an "entry file", and provides a helper object for you to load (and initialize) needed modules on demand. Since the helper object is "generated", it needs not "prepare some data and parse it" to find out correct module you want: such logics are hard-coded in the helper object.

## License

Any version of MIT, GPL or LGPL.
