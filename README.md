# remote-shell

Just for fun!

### Run with docker

Build an image
```
$ docker build -t oncesk-remote-shell:v1 .
```

Start server
```
$ docker run -e SHELL_HOST=0.0.0.0 -e SHELL_PORT=80 -e SHELL_DEBUG=1 --rm -p 8080:80 -v $(pwd):/app oncesk-remote-shell:v1 /app/bin/server
```

Connect
```
$ telnet localhost 8080
```
