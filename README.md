# FrameFlow

**A lightweight, human-readable streaming protocol for PHP 8.4+**

FrameFlow is designed for developers who need a robust way to stream data (payloads) accompanied by rich metadata. It bridges the gap between simple line-based logs and complex binary formats. It's built for speed, integrity, and absolute simplicity.

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-8892bf.svg?style=flat-square)](https://www.php.net/releases/8.4/en.php)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

---

## Key Features

- **Human Readable:** Inspect your stream with a simple text editor.
- **Raw Payload Support:** Transmit arbitrarily complex payloads (HTML, CSS, JavaScript, JSON) without escaping.
- **No Encoding Overhead:** No need for Base64 or other encodings — payloads stay in their original form.
- **Robust Integrity:** Automatic `sha256` checksums and `Payload-Length` headers for every frame.
- **Modern PHP 8.4:** Fully utilizes Property Hooks and Asymmetric Visibility for maximum performance and clean code.
- **Zero Dependencies:** No vendor bloat. Just pure, high-performance PHP.
- **Time-Ordered:** Built-in RFC 9562 compliant **UUIDv7** for natural sorting of messages.

---

## The Protocol Structure

Each FrameFlow message consists of a global Header and one or more Frames.

```txt
--- FrameFlow-1.0 ---
Trace-Id: 000069d3-c5d9-7bb4-c757-48eb16eac000
Timestamp: 2026-04-09T22:40:25+00:00
Charset: utf-8
Version: 1
Checksum: sha256
Environment: Production
---

--- Frame ---
Id: 69d8017e8e6f2
Type: message
Payload-Length: 12
Checksum: f48... (sha256)
Payload:
Hello World!
---
```

## Quick Start

### Installation

```bash
composer require sobi-labs/frameflow
```

## Basic Usage
```php
use FrameFlow\Message;
use FrameFlow\Encoder;

// 1. Create a message with global metadata
$message = Message::make();
$message->header->meta->add('Environment', 'Production');

// 2. Add frames (can be simple strings or Frame objects)
$message->add("This is the first part of my data.");
$message->add("And here is more content...");

$id = uniqid();
$frame = new Message\Frame($message->header, $id, 'notification', new Message\Meta([
    "Content-Type" => "text/html; charset=utf-8",
    "Action" => "ShowAlert",
]));
$frame->payload = "<div>At least an HTML content</div>";
$message->add($frame);

// 3. Encode to streamable string
echo Encoder::encode($message);
```

### Output
```text
--- FrameFlow-1.0 ---
Trace-Id: 019d7423-cb85-714c-8f49-a58077393a47
Timestamp: 2026-04-09T21:26:36+00:00
Charset: utf-8
Version: 1
Checksum: sha256
Environment: Production
---

--- Frame ---
Id: 69d8198c287f2
Payload-Length: 34
Checksum: b6ed69fd1e91020ca3e251f91d8bdc07e60c5f8426aba34b06a502e5bfe13ae1
Payload:
This is the first part of my data.
---

--- Frame ---
Id: 69d8198c28805
Payload-Length: 27
Checksum: 3aabc50a531c0c9251c8419ce676c953395fd576ed6b9720503232a14198d6ad
Payload:
And here is more content...
---

--- Frame ---
Id: 69d8198c28808
Content-Type: text/html; charset=utf-8
Action: ShowAlert
Payload-Length: 35
Checksum: d32792d07b545c96fdefa95cd014ba79f4f4617131d0f9d632ad8d144754ca86
Payload:
<div>At least an HTML content</div>
---
```

## Advanced Concepts  
### Property Hooks & Metadata  
FrameFlow uses PHP 8.4 property hooks. This means `size` and `checksum` are calculated on-the-fly only when needed, ensuring your data is always consistent without manual updates.  

```php
$frame = $message->get($id);
echo $frame->size;     // Returns byte length
echo $frame->checksum; // Returns current hash of payload
```

### Metadata Escaping
FrameFlow automatically handles multi-line metadata by escaping line breaks (`\n`, `\r`) during encoding. This ensures the protocol structure remains intact even with complex meta-information.  

## Specification Highlights
1. __Delimiters__: Blocks are separated by `---`.  
2. __Metadata__: Key-Value pairs followed by a colon and a space.  
3. __Payloads__: Prefixed by `Payload-Length` to allow safe binary data transmission.
4. __IDs__: Every frame has an ID. If not provided, FrameFlow generates sequential IDs for you.

## License
The MIT License (MIT). Please see License File for more information.  

## Roadmap
The vision for **FrameFlow** is to provide a seamless, transparent way to stream data and metadata. Below is our strategic path forward:

## Phase 1: Core & PHP Ecosystem (Current)
- [x] Base data structures (Message, Frame, Meta)
- [x] RFC 9562 compliant UUIDv7 integration (Zero-Dependency)
- [x] PHP 8.4 high-performance Encoder
- [ ] **Next:** High-performance PHP Decoder (Streaming Parser)
- [ ] Comprehensive PHPUnit test suite (Aiming for 100% code coverage)
- [ ] Static analysis integration (PHPStan/Psalm)

## Phase 2: Cross-Language Support
- [ ] **JavaScript/TypeScript Client:** A browser-compatible decoder for `fetch()` and WebStreams API.
- [ ] **Cross-Platform Specs:** Formalization of the FrameFlow specification for third-party implementations.
- [ ] **Go/Rust Ports:** High-performance implementations for microservice environments.

## Phase 3: Advanced Features
- [ ] **Binary Frames:** Optimized handling for raw binary data to minimize encoding overhead.
- [ ] **Compression:** Optional frame-level compression (e.g., zstd, gzip).
- [ ] **Encryption:** Native support for AEAD (Authenticated Encryption with Associated Data) at the frame level.

Built with ☕ and ❤️ for the modern PHP ecosystem.