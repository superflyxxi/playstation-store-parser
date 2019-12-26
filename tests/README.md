# Usage

This is assumed to be run within the `tests` directory just for ease of explanation.

## Building

```sh
docker build --tag ps-store:test --build-arg FROM_IMAGE=ps-store:build .
```

## Running all

```sh
docker run -t --rm ps-store:test
```

## Running specific test

```sh
docker run -t --rm ps-store:test TestFile.php
docker run -t --rm ps-store:test TestDir/
```

