# _*_ coding: UTF-8 _*_
import sys,os,random,time,hmac,hashlib
#def get_header(uri,body="",method="GET"):
def get_header(uri,method="GET"):
    """
    @param:
    @note:返回请求平台所需的http头部
    @return:返回头部字典
    """
    SECRET_ID="AKIDEnyw5wgK3RWKshkDCvROgkgXuwyHle7g"
    SECRET_KEY="TV38gvuergm2VOMcJGuIE3sML1yKLRHZ"
    body = "{ \"devicesList\": [ { \"lanIp\":\"10.190.165.228\", \"port\":8008 } ] }"
    headers = {
            "x-txc-cloud-secretid":SECRET_ID,
           # "x-txc-cloud-nonce":random.randint(1,(1<<31) - 1),
            "x-txc-cloud-nonce":9932195,
           # "x-txc-cloud-timestamp":int(time.time()),
            "x-txc-cloud-timestamp":1384260724
            }

    auth = {
            "uri":uri,
            "method":method,
            "body":body,
            "secretid":headers["x-txc-cloud-secretid"],
            "nonce":headers["x-txc-cloud-nonce"], 
            "timestamp":headers["x-txc-cloud-timestamp"]
        }
    p = "body=%(body)s&method=%(method)s&uri=%(uri)s&x-txc-cloud-secretid=%(secretid)s&x-txc-cloud-nonce=%(nonce)s&x-txc-cloud-timestamp=%(timestamp)s" % auth
    print p
    signature = hmac.new(SECRET_KEY, p, hashlib.sha1).digest().encode('base64').rstrip()
    headers.update({"x-txc-cloud-signature":signature})

    return headers
      
if __name__=="__main__":
	print get_header("/v1/domains/100000068130/cvm_unbind")
