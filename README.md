# XMPPFrameworkLogger

An iOS jailbreak tweak to log XMPP communication.

## Background

[XMPP](https://xmpp.org/) is a protocol for real-time communication, most commonly understood as used in chat apps. [XMPPFramework](https://github.com/robbiehanson/XMPPFramework) is a popular Objective-C XMPP framework. XMPP opens a TCP socket and the XMPP standard dictates the use of TLS. [tcpdump](http://www.tcpdump.org/)'s output is thus garbled nonsense and traditional HTTPS MITM proxies, e.g. [Charles](https://www.charlesproxy.com/), [mitmproxy](https://mitmproxy.org/), don't provide the tooling to supply a certificate of our choosing in this case (it's not HTTP).

Fortunately, in Objective-C, when a method is called, the memory location of the class's method is looked up in table using the method's name as a string. This table can be altered at runtime, allowing replacing classes' method implementations with our own. This is called [swizzling](https://nshipster.com/method-swizzling/).

On iOS this requires a jailbroken iPhone.

[Theos](https://github.com/theos/theos) is a suite of development tools which allows for easy swizzling. 

XMPPFramework uses [CocoaAsyncSocket](https://github.com/robbiehanson/CocoaAsyncSocket) for its underlying socket. This tweak swizzles CocoaAsyncSocket's GCDAsyncSocket's [writeData method](https://github.com/robbiehanson/CocoaAsyncSocket/blob/master/Source/GCD/GCDAsyncSocket.m#L5838-L5857) and its delegate [didReadData method](https://github.com/robbiehanson/CocoaAsyncSocket/blob/master/Source/GCD/GCDAsyncSocket.h#L1104-L1108) in XMPPStream, outputting the NSData XML string to NSLog.

Morally, we have every right to know what data our phones are sending. This tweak could be used as a base to drop XMPP messages you would rather not sent, ala ad-blocking.

## Installation

In Terminal, SSH to your jailbroken iOS device:

`ssh root@192.168.0.0`

The default password is `alpine`.

Download using:

`curl -s "https://api.github.com/repos/BrianHenryIE/XMPPFrameworkLogger/releases/latest" | grep '"browser_download_url":' | sed -E 's/.*"([^"]+)".*/\1/' | xargs -I browser_download_url curl -o ie.brianhenry.xmppframeworklogger.deb browser_download_url -L`

Install using:

`dpkg -i ie.brianhenry.xmppframeworklogger.deb`

To remove:

`dpkg -r ie.brianhenry.xmppframeworklogger`

## Use

Once installed, the tweak will run in any application with XMPPFramework's [XMPPStream](https://github.com/robbiehanson/XMPPFramework/blob/master/Core/XMPPStream.h) class (since that's where the communication terminates).

To view the logs, open Console on MacOS, select your iOS device, and search "XMPPFramework".

## Acknowledgements

Thank you to my friends Eoin and Roisín for the iPhone I had spare to jailbreak, my wife Leah for her patience, and [Dustin Howett](https://github.com/DHowett) for his help on IRC which pushed it over the line.