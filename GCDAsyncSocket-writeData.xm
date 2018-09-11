%hook GCDAsyncSocket
- (void)writeData:(NSData *)data withTimeout:(NSTimeInterval)timeout tag:(long)tag {
	NSString* xml = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
	NSLog(@"XMPPFramework send:   \n%@", xml);
	%orig;
}
%end
