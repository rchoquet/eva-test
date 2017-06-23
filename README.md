# Refactoring Kata Test

## The refactoring explained

Some of the most notable steps:
- Removing the singleton anti-pattern across the codebase as it makes global state leaking everywhere
- Removing the handmade classes loading by using composer PSR-4 autoload
- Actually refactoring the TemplateManager logic:
    - Use composition over hidden singleton dependencies
    - rename some cryptic vars
    - escape HTML to prevent XSS
    - add some DocBlocks
    - throws exception when a quote was not given but we need it to properly render the given template
    - add a test for this exception
- ✨ Using emojis in commit message ✨

*Nb: I thought about removing the whole rendering mechanism (just keeping the public function) and replacing this by a
common template engine (as there is not point in re-inventing the wheel) but I didn't know if this was allowed
as most of the formatting logic would have been moved to the templates*

*Nb2: I didn't tested the whole thing, as in real world the formatting part wouldn't have been done in the PHP class*

## Introduction

**Evaneos** is present on a lot of countries and we have some message templates we want to send
in different languages. To do that, we've developed `TemplateManager` whose job is to replace
placeholders in texts by travel related information.

`TemplateManager` is a class that's been around for years and nobody really knows who coded
it or how it really works. Nonetheless, as the business changes frequently, this class has
already been modified many times, making it harder to understand at each step.

Today, once again, the PO wants to add some new stuff to it and add the management for a new
placeholder. But this class is already complex enough and just adding a new behaviour to it
won't work this time.

Your mission, should you decide to accept it, is to **refactor `TemplateManager` to make it
understandable by the next developer** and easy to change afterwards. Now is the time for you to
show your exceptional skills and make this implementation better, extensible, and ready for future
features.

Sadly for you, the public method `TemplateManager::getTemplateComputed` is called everywhere, 
and **you can't change its signature**. But that's the only one you can't modify (unless explicitly
forbidden in a code comment), **every other class is ready for your changes**.

This exercise **should not last longer than 1 hour** (but this can be too short to do it all and
you might take longer if you want).

You can run the example file to see the method in action.

## Rules
There are some rules to follow:
 - You must commit regularly
 - You must not modify code when comments explicitly forbid it

## Deliverables
What do we expect from you:
 - the link of the git repository
 - several commits, with an explicit message each time
 - a file / message / email explaining your process and principles you've followed

**Good luck!**
