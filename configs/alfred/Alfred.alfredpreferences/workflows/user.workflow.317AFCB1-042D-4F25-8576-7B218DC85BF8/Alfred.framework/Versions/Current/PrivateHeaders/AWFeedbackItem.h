//
//  AWFeedbackItem.h
//  AlfredWorkflow
//
//  Created by Daniel Shannon on 5/24/13.
//  Copyright (c) 2013 Daniel Shannon. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface AWFeedbackItem : NSObject {
    @private
    NSArray *tags_;
    NSDictionary *attrib_;
}

+ (id)itemWithObjectsAndKeys:(id)o, ... NS_REQUIRES_NIL_TERMINATION;
- (id)initWithObjects:(NSArray *)obj forKeys:(NSArray *)key;

- (NSString *)xml;


@property NSString      *title;
@property NSString      *subtitle;
@property NSString      *uid;
@property NSNumber      *valid;
@property NSString      *autocomplete;
@property NSString      *icon;
@property NSNumber      *fileicon;
@property NSNumber      *filetype;
@property NSString      *arg;
@property NSString      *type;

@end
