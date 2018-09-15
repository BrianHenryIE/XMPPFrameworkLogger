%hook XMPPStream
- (void)socket:(id)sock didReadData:(NSData *)data withTag:(long)tag {
        NSString* xml = [[NSString alloc] initWithData:data encoding:NSUTF8StringEncoding];
       
        NSUInteger length = [xml length];
        
        int divide_factor = 900;
        
        if(length > divide_factor) {

	    NSString *isFirst = @"-start";            
            
	    NSMutableString *mutatingXmlStr = [xml mutableCopy];
            
            while (mutatingXmlStr.length) {
                
                NSString* substring = [mutatingXmlStr substringWithRange:NSMakeRange(0, MIN(divide_factor, mutatingXmlStr.length))];
                
                mutatingXmlStr = [[mutatingXmlStr stringByReplacingCharactersInRange:NSMakeRange(0, MIN(divide_factor, mutatingXmlStr.length)) withString:@""] mutableCopy];
                
		NSLog(@"XMPPFramework receive-partial%@: %@\n", isFirst, substring);
                
		isFirst = @"-contd";
            }
        } else {
            NSLog(@"XMPPFramework receive:               %@", xml);
        }
        
        %orig;
}
%end

