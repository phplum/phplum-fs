# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][1], and this project adheres
to [Semantic Versioning][2].

## [1.0.0] - 2024-03-12

### Added

- Class `Path` to perform operations on paths.
- Class `FileSystem` to perform operations on file system.
- Class `IOException` for general I/O exceptions.
- Class `FileNotException` for file-not-found exceptions.
- Property `Path::parent` for parent path.
- Method `FileSystem::mkdir` to create directories.
- Method `FileSystem::unlink` to remove files or symbol links.
- Method `FileSystem::rm` to remove files or directories recursively.
- Method `FileSystem::writeFile` to write data to files.

[1]: https://keepachangelog.com/en/1.0.0/

[2]: https://semver.org/spec/v2.0.0.html
