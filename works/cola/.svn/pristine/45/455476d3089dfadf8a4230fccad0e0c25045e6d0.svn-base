namespace php Thrift

/**
 * 
 */
exception ServiceException {
  1: i32 what,
  2: string why
}

/**
 * 
 */
service CsisService {
   string request(1:string name, 2:string method, 3:string jsonargs) throws (1:ServiceException ouch),
}
