# MC-NBT Schematica demo.

Composes a new `schematic` with 4 blocks marking corners of one horizontal and one vertical edge, and a single block marking the "base point", then saves the data using CompressedWriter class.

Also dumps composed structure to the standard output.

## Test schematic description in detail

North plane with anchor quartz block in the center and two cobblestone corners along the bottom edge, plus two corners along the south-eastern vertical edge.

## Nota bene:

Minecraft's "up" direction is "y" coordinate. Schematica, to the contrary, correctly defines height as "Z" and stores schematics as horizontal slices of longitudal bars.

Thus, position to save block in the data array would be (on a base 0 inside the block):
```
( y * L + z ) * W + X (Minecraft)
( Z * L + Y ) * W + X (Schematica)
```
