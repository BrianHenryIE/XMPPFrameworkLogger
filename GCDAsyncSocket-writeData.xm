%hook GCDAsyncSocket
- (void)writeData:(NSData *)data withTimeout:(NSTimeInterval)timeout tag:(long)tag {
	NSString* xml = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
        
	NSUInteger length = [xml length];
        
        int divide_factor = 950;

	NSString *isFirst = @"-start";
        
        if(length > divide_factor) {
            
            NSMutableString *mutatingXmlStr = [xml mutableCopy];
            
            while (mutatingXmlStr.length) {
                
                NSString* substring = [mutatingXmlStr substringWithRange:NSMakeRange(0, MIN(divide_factor, mutatingXmlStr.length))];
                
                mutatingXmlStr = [[mutatingXmlStr stringByReplacingCharactersInRange:NSMakeRange(0, MIN(divide_factor, mutatingXmlStr.length)) withString:@""] mutableCopy];
                
                NSLog(@"XMPPFramework send-partial%@:    %@\n", isFirst, substring);

		isFirst = @"-contd";
            }
        } else {
            NSLog(@"XMPPFramework send:                  %@", xml);
        }
 
	%orig;
}
%end
