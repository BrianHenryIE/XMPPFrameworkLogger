%hook XMPPStream
- (void)socket:(id)sock didReadData:(NSData *)data withTag:(long)tag {
        NSString* xml = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
        NSLog(@"XMPPFramework receive:\n%@", xml);
        %orig;
}
%end

