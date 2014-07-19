#!/usr/bin/python2
import sys
import urllib2
import thread
import time
import os
import os.path
import tty
import termios

class pget(object): 
    def __init__(self, url, path=".", logfile=None): 
        self.paused   = False
        self.progress = 0
        self.path     = path
        self.url      = url
        self.logfile  = logfile
    
    def log(self, msg, nn=False):
        if self.logfile != None: 
            with open(self.logfile, "a") as f:
                f.write(msg.strip() + "\n")
        else: 
            if nn: 
                print msg, 
            else: 
                print msg
    
    def get(self): 
        n = self.url.split("/")[-1]
        N = os.path.join(self.path, n)
        self.tempname = N + ".pydownload"
        
        if os.path.exists(self.tempname):
            mode = "ab"
            self.r = urllib2.Request(self.url, headers={"Range": "bytes=%s-"%os.path.getsize(self.tempname)})
        else: 
            mode = "wb"
            self.r = urllib2.Request(self.url)
        
        self.u = urllib2.urlopen(self.r)
        
        with open(self.tempname, mode) as f: 
            m = self.u.info()
            fs = int(m.getheaders("Content-Length")[0])

            bs = 8192
            d = 0
            
            waitForConnection = False
            while True: 
                if self.paused: 
                    time.sleep(0.5)
                    waitForConnection = True
                else: 
                    if waitForConnection: 
                        time.sleep(1)
                        waitForConnection = False
                    
                    if self.u == None: 
                        time.sleep(0.5)
                        continue
                    b = self.u.read(bs)
                    if not b: 
                        break

                    d += len(b)
                    f.write(b)

                    self.progress = d*100.0 / fs
                    o = n + ": %0.2f"%self.progress
                    self.log(o + " "*len(o) + "\r", True)
        self.u.close()
        
        os.rename(self.tempname, N)
        os._exit(1)
        
    def togglePause(self): 
        if self.paused: 
            self.u = urllib2.urlopen(self.r)
        else: 
            self.u.close()
            self.u = None
            self.r = urllib2.Request(self.url, headers={"Range": "bytes=%s-"%os.path.getsize(self.tempname)})
            
        self.paused = not self.paused
    
    def read(self): 
        fd = sys.stdin.fileno()
        s = termios.tcgetattr(fd)
        c = None
        try: 
            tty.setraw(sys.stdin.fileno())
            c = ord(sys.stdin.read(1))
        finally: 
            termios.tcsetattr(fd, termios.TCSADRAIN, s)
        return c

if __name__ == "__main__": 
    l = len(sys.argv)
    if l == 2: 
        p = pget(sys.argv[1])
    elif l == 3: 
        p = pget(sys.argv[1], sys.argv[2])
    elif l == 4: 
        p = pget(sys.argv[1], sys.argv[2], sys.argv[3])
    else: 
        print "Usage: pget.py URL [OUTPUT_DIR] [LOGFILE]"
        sys.exit(1)

    thread.start_new_thread(p.get, ())

    while p.progress != 100: 
        i = p.read()
        if i == 32: 
            p.togglePause()
            if p.paused: 
                p.log("[Paused]: %0.2f"%p.progress)