# Driver notes

## JS

`Connection` - Constructor gets URL and options, sends messages over WS.
`Client` - Consumes `Connection`. Builds request and calls `Connection::submit()` / `Connection:stream()`.
`DriverRemoteConnection` - Consumes `Client`. Handles session and transactions.

## Python

`AbstractTransport` - Abstracts the actual communication layer, in their case `aiohttp`.
`Protocol` - Formats request and calls interceptors, then sends to transport. Decodes chunks received by the transport
`Connection` - Consumes the `Protocol` and a `TransportFactory`, composes `ResultSet`, passes data chunks to `Protocol`
`Client` - Maintains a pools of `Connection`s. Submits messages by getting `Connection` from pool and writing to it. 
     Uses a `ThreadPoolExecutor`. What's that? (something async-y)

This is a better fit for us if we plan to allow different packages for the communication side (ie AMPHP or Swoole). But
the `Protocol` bit looks overkill for v4. Unless we decide to support WS and v3, we should be able to move that stuff
to the `Connection`.

The question now is connection pooling. AMP boasts connection pools somewhere behind the scenes, but the documentation
on that 404s :(. For Swoole we'd need to do it ourselves. The question is, do we leave that to the transport layer or 
pool our own `Connection`? If it's at the transport layer and the connection is deserializing the stream, how does it 
know which result set it's getting chunks for?


## Do we return a `Future` or not?

### Pros

* Users can start processing results as soon as the first batch is received

### Cons

* It's an unfamiliar paradigm for most PHP devs

Actually, the way AMP do it is largely transparent to the consumer. But figuring out _how_ they do that
is not easy.


## Connection pooling

Other GLVs pool the `Connection` object. AMPHP handles connection pooling under the hood, and a Swoole transport will 
likely need a swoole-specific pool for the client (not sure on quite how we implement that yet).

So no, we don't pool the `Connection` at this point.