<?php

declare(strict_types=1);

namespace Kynx\Gremlin\Structure\Io\Binary;

use Kynx\Gremlin\Structure\Io\Binary\Exception\DomainException;
use ValueError;

use function chr;
use function ord;

/**
 * @see https://tinkerpop.apache.org/docs/3.7.3/dev/io/#_data_type_formats
 */
enum BinaryType: int
{
    public const string FLAG_NONE = "\x00";
    public const string FLAG_NULL = "\x01";

    case Int                   = 0x01;
    case Long                  = 0x02;
    case String                = 0x03;
    case Date                  = 0x04;
    case Timestamp             = 0x05;
    case ClassName             = 0x06;
    case Double                = 0x07;
    case Float                 = 0x08;
    case List                  = 0x09;
    case Map                   = 0x0a;
    case Set                   = 0x0b;
    case UUID                  = 0x0c;
    case Edge                  = 0x0d;
    case Path                  = 0x0e;
    case Property              = 0x0f;
    case TinkerGraph           = 0x10;
    case Vertex                = 0x11;
    case VertexProperty        = 0x12;
    case Barrier               = 0x13;
    case Binding               = 0x14;
    case Bytecode              = 0x15;
    case Cardinality           = 0x16;
    case Column                = 0x17;
    case Direction             = 0x18;
    case Operator              = 0x19;
    case Order                 = 0x1a;
    case Pick                  = 0x1b;
    case Pop                   = 0x1c;
    case Lambda                = 0x1d;
    case P                     = 0x1e;
    case Scope                 = 0x1f;
    case T                     = 0x20;
    case Traverser             = 0x21;
    case BigDecimal            = 0x22;
    case BigInteger            = 0x23;
    case Byte                  = 0x24;
    case ByteBuffer            = 0x25;
    case Short                 = 0x26;
    case Boolean               = 0x27;
    case TextP                 = 0x28;
    case TraversalStrategy     = 0x29;
    case BulkSet               = 0x2a;
    case Tree                  = 0x2b;
    case Metrics               = 0x2c;
    case TraversalMetrics      = 0x2d;
    case Merge                 = 0x2e;
    case DT                    = 0x2f;
    case UnspecifiedNullObject = 0xfe;
    case Custom                = 0x00;
    case Char                  = 0x80;
    case Duration              = 0x81;
    case InetAddress           = 0x82;
    case Instant               = 0x83;
    case LocalDate             = 0x84;
    case LocalDateTime         = 0x85;
    case LocalTime             = 0x86;
    case MonthDay              = 0x87;
    case OffsetDateTime        = 0x88;
    case OffsetTime            = 0x89;
    case Period                = 0x8a;
    case Year                  = 0x8b;
    case YearMonth             = 0x8c;
    case ZonedDateTime         = 0x8d;
    case ZoneOffset            = 0x8e;

    public static function fromChr(string $chr): self
    {
        try {
            return self::from(ord($chr));
        } catch (ValueError $exception) {
            throw DomainException::unknownBinaryType(ord($chr), $exception);
        }
    }

    public function toChr(): string
    {
        return chr($this->value);
    }
}
