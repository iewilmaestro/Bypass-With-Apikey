import requests,time,json,sys,os

def setCaptcha(Apikey,SiteUrl,SiteKey):
    url='https://api.anycaptcha.com/';
    header={'Host': 'api.anycaptcha.com','Content-Type': 'application/json'}
    data={
        'clientKey': Apikey,
        'task':{
            'type':'RecaptchaV2TaskProxyless',
            'websiteURL':SiteUrl,
            'websiteKey':SiteKey,
            'isInvisible':False
        }
    }
    awal = requests.post(url+'createTask',json=data,headers=header).text
    task = json.loads(awal)
    if task["taskId"] == False:
        print("saldo abis kira-kira")
    data = {
        'clientKey': Apikey,
        'taskId' : task["taskId"]
    }
    while True:
        print("wait for solution",end="\r")
        news = requests.post(url+"getTaskResult",json=data,headers=header).text
        rest = json.loads(news)
        if rest['status'] == 'processing':
            print("sedang memproses",end="\r")
            time.sleep(7)
            continue
        return rest['solution']['gRecaptchaResponse']
    

ss=setCaptcha('Apikey','https://api.anycaptcha.com','SiteKey')
print(ss)
