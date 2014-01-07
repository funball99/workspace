import sys
import json
import traceback
import SocketServer
from daemon import Daemon
import jsbeautifier

class Todo:
    def __init__(self):
        print('Welcome!')
    def test(self, args):
        res = jsbeautifier.beautify(args[0].encode('utf-8'))
        return res;
    def error(self, args):
        return 'not function!'

class ThreadedTCPRequestHandler(SocketServer.BaseRequestHandler):
    def handle(self):
        while True:
            try:
                data = self.request.recv(2*1024*1024)
                if not data:
                    print('end')
                    break
                data = json.loads(data)
                res =  getattr(self._object, data['func'], 'error')(data['args'])
                if not res:
                    res = ''
                res = str(len(res)).rjust(8, '0') + str(res)
                self.request.send(res)
            except:
                print('error in ThreadedTCPRequestHandler :%s, res:%s' % (traceback.format_exc(), data))

class ThreadedTCPServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer):
    pass

class Server(Daemon):        
    def conf(self, host, port, obj):
        self.host = host
        self.port = port
        self.obj = obj
        ThreadedTCPServer.allow_reuse_address = True
    def run(self):
        ThreadedTCPRequestHandler._object = self.obj
        server = ThreadedTCPServer((self.host, self.port), ThreadedTCPRequestHandler)
        server.serve_forever()

if __name__ == '__main__':
    server = Server('/tmp/daemon-tortoise.pid')
    server.conf('0.0.0.0', 1990, Todo())
    if len(sys.argv) == 2:
        if 'start' == sys.argv[1]:
            server.start()
        elif 'stop' == sys.argv[1]:
            server.stop()
        elif 'restart' == sys.argv[1]:
            server.restart()
        else:
            print("Unknown command")
            sys.exit(2)
        sys.exit(0)
    else:
        print("usage: %s start|stop|restart" % sys.argv[0])
        sys.exit(2)
