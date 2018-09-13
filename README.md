# XMPPFrameworkLogger

An iOS jailbreak tweak to log XMPP communication.

## Background

[XMPP](https://xmpp.org/) is a protocol for real-time communication, most commonly understood as used in chat apps. [XMPPFramework](https://github.com/robbiehanson/XMPPFramework) is its most popular Objective-C library. XMPP opens a TCP socket and the XMPP standard dictates the use of TLS. Thus, after the TLS connection is negotiated, [tcpdump](http://www.tcpdump.org/)'s output is garbled nonsense. Traditional HTTPS MITM proxies, e.g. [Charles](https://www.charlesproxy.com/), [mitmproxy](https://mitmproxy.org/), don't provide the tooling to supply a certificate of our choosing in this case (it's not HTTP).

Fortunately, in Objective-C, when a method is called, the memory location of the class's method is looked up in table using the method's name as a string for reference. This table can be altered at runtime, allowing replacing classes' method implementations with our own. This is called [swizzling](https://nshipster.com/method-swizzling/).

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

Once installed, the tweak will run in any application containing XMPPFramework's [XMPPStream](https://github.com/robbiehanson/XMPPFramework/blob/master/Core/XMPPStream.h) class (since that's where the communication terminates).

To view the logs, open Console on MacOS, select your iOS device, and search "XMPPFramework".


[![](https://brianhenryie.s3.amazonaws.com/2018/xmppframeworklogger-console600w.png)](https://brianhenryie.s3.amazonaws.com/2018/xmppframeworklogger-console.png)




### Better logs

The Console logs contain all the necessary information, but are very hard to make a mental model from. I've written a script to make them a little easier on the eye:

[![](https://brianhenryie.s3.amazonaws.com/2018/xmppframeworklogger-formattedxml600w.png)](https://brianhenryie.s3.amazonaws.com/2018/xmppframeworklogger-formattedxml.png)

Messages from the client are highlighted blue and responses from the server in orange. XML is indented, JSON inside <json> tags is formatted using PHP [JSON\_PRETTY\_PRINT](http://php.net/manual/en/function.json-encode.php) and style is applied with Google's [code-prettify](https://github.com/google/code-prettify) library.

To save the iOS logs to file, use [deviceconsole](https://github.com/rpetrich/deviceconsole/). Install via [npm](https://www.npmjs.com/get-npm) using: 

`npm install deviceconsole`

Then output the logs to file using:

`deviceconsole > session_ref.xmpp.log`

Once a `.xmpp.log` file is in the same folder as this project's `formatter/formatlogfile.php`, running:

`php formatlogfile.php`

Will output a `.xmpp.log.html` for every `.xmpp.log` file in the same directory.


## Acknowledgements

Thank you to my friends Eoin and Roisín for the iPhone I had spare to jailbreak, my wife Leah for her patience, and [Dustin Howett](https://github.com/DHowett) for his help on [IRC](https://kiwiirc.com/client/irc.saurik.com:+6697/#theos) which pushed it over the line.