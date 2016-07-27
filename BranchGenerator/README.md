# MC-NBT BranchGenerator demo.

This elaborate example demonstrates creation of a minecraft .schematic file from a generated set of primitives.

The main worker is, in fact, the `BranchTrunk` class.

`BranchGenerator.php` provides the wrapping around generation method and assembling the final set of trunks for rendering, as well as calculating a number of complimentary variables.

`image.php` renders the image of a generated branch in top-down and side view.

`schematic.php` saves an actual schematic.

To change the generated branch, set the _GET[seed] to a new value. Or, if you are trying this in a browser, remove the relevant assignment and it'll redirect you to a newly generated seed.

If you want to alter the generator, inherit the `BranchTrunk` class and make your own implementation of its ::next() method.

Make sure it returns the array of new trunks. Or an empty array.

Implement some means to indicate that a trunk must not branch/grow further. I.e. by clearing(not setting) its branching/trunking settings, as in the example.
