React HTTP Server Sandbox
=========================

This is a mock up of a HTTP server for the Nette Sandbox.

It creates a new DI container for each request. It was much easier to implement that reusing of DI container, because HTTP request, HTTP response and session are registered as services, so it would require changes in framework.

An average HTTP request to homepage takes about 3 ms. (I measured it by command `ab -n 1000 -c 20 'http://127.0.0.1:8000/'` on MacBook Pro Late 2013).

There are some memory leaks, so it runs out of memory after 2300 requests on my computer. I haven't investigated what causes them yet.

The React HTTP server is unstable, so it is not ready for production environment.
