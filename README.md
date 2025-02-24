# kynx/gremlin

Talk [Gremlin] to graph databases.

## Plan

Start with types, move to the connection end of the problem, then chew away from both ends until we reach the middle.

1. Basic binary type serialization
2. Remote driver to point where it can authenticate and read chunked response
3. Sending a traversal
4. Mapping to PHP objects
5. ...

## Future

See:
https://tinkerpop.apache.org/docs/3.7.4-SNAPSHOT/dev/future/#io-updates

1. How far off is this? Not needing websockets would be grand. Update: beta1 is out, remote drivers are catching up.
2. Seems Go (at least) is getting v4 features, ~~but there isn't a 4.x-dev branch~~ (it's all on `main`)
3. There's mention of changes to some binary types. What are they?

Decision: go all in on v4, add WS + v3 binary types later if needed.

## TODO

* [ ] Review https://tinkerpop.apache.org/docs/3.7.3/dev/provider/#gremlin-semantics-equality-comparability 
for type comparison semantics.



[Gremlin]: https://tinkerpop.apache.org/gremlin.html