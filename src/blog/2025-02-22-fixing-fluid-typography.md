---
title: Fixing fluid typography with one line of JS
description: Fluid typography is great. But there are some issues with the most common approaches that can interfere with user preferences.
preamble: Fluid typography is great. But there are some issues with the most common approaches that can interfere with user preferences.
---

Who doesn't love a really large heading when using a full screen window on a large monitor? Using fluid typography, we can make sure those large headings also look great on smaller screens. I have used [utopia.fyi](https://utopia.fyi) for years to setup fluid font sizes, it's a genius tool. But I recently read a [blog post by Miriam Suzanne](https://www.oddbird.net/2025/02/12/fluid-type/) about respecting the user-preferred font size, and how these fluid typography solutions might fall short.

## User-preferred font size?

There is a setting in your browser that you can use to define what the base font size should be. By default it is set to 16px, but if you prefer a larger font size you can set it to your liking. This will change the *base font size*, which means, the size of unstyled body text. Headings are set to some multiple of this base font size. Level 1 headings are usually set to 2em, so if you set your base font size to 20px, an H1 will be 40px.

## Don't set a root font size

Font sizes shouldn't be set in px, they should be set in rem. There should be no font size set on the root element and (pretty much) all font sizes should be set to some rem or em value. That way, font sizes will adapt to the user-preferred size.

## The problem with fluid typography

The problem is that most people *assume* that the base font size is always 16px. The utopia.fyi fluid type tool makes this assumption as well. When the base font size is *not* 16px, there are some miscalculations. 

If a user sets their browser's base font size to 24px, a min/max viewport width set to 320/1600 becomes 480/2400:

```
320 / 16 * 24 = 480 ... 1600 / 16 * 24 = 2400
```

The font sizes themselves will also be off, a "step 0" font size of 18/24 becomes 27/36. Instead of the text going from 18px at a 320px viewport to 24px at a 1600px viewport, the text would go from 27px at a 480px viewport to 36px at a 2400px viewport. Not what the designer intended, and not what the user asked for.

## One line of JS to fix fluid type

As long as we don't do anything foolish like setting our own base font size in CSS, 1rem will be whatever the user has set as their preference. But when creating fluid sizes, we need to do some px to rem conversions, which is where we have to make some assumption about the base font size. But what if we could get *the actual* user-preferred base font size as a unitless value in our CSS?

Behold:

```js
document.documentElement.style.setProperty(
    '--root-size', 
    window.getComputedStyle(document.documentElement).fontSize.replace('px', '')
);
```

This sets a CSS variable on the html element with a unitless value of whatever the user has set as their base font size. Now, all we have to do is change all of the assumed values of 16 in our fluid type CSS setup to `var(--root-size)`:

```css
/* Fluid type setup */
:root {
  --root-size: 16; /* Fallback, overwritten by the JS code above */
  --fluid-min-width: 320;
  --fluid-max-width: 1600;

  --fluid-screen: min(100vw, calc(var(--fluid-max-width) * 1px));
  --fluid-bp: calc((var(--fluid-screen) - var(--fluid-min-width) / var(--root-size) * 1rem) / (var(--fluid-max-width) - var(--fluid-min-width)));

  --min-scale: 1.2;
  --max-scale: 1.414;

  --size-md-min: calc(var(--root-size) * 0.9); /* Slightly smaller on the smallest screens */
  --size-md-max: calc(var(--root-size) * 1.1); /* Slightly larger on the largest screens */
  --size--md: calc(((var(--size-md-min) / var(--root-size)) * 1rem) + (var(--size-md-max) - var(--size-md-min)) * var(--fluid-bp));

  --size-lg-min: calc(var(--size-md-min) * var(--min-scale));
  --size-lg-max: calc(var(--size-md-max) * var(--max-scale));
  --size--lg: calc(((var(--size-lg-min) / var(--root-size)) * 1rem) + (var(--size-lg-max) - var(--size-lg-min)) * var(--fluid-bp));  

  /* ...and so on */
}
```

Out body text `var(--size--md)` is now set to the user-preferred base font size. And all other sizes are relative to that. [When JS fails](https://medium.com/@jason.godesky/when-javascript-fails-52eef47e90db), we fallback to the 1rem = 16px assumption. But one line of JS can *progressively enhance* the experience for people with a custom base font size. 