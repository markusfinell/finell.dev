---
title: An improved grid from "Every Layout"
description: An improved version of The Grid from Every Layout by Andy Bell and Heydon Pickering.
---

I am a big fan of [Andy Bell](https://andy-bell.co.uk) and [Heydon Pickering](https://heydonworks.com). They want to build the best possible experiences for everyone. They do it by building the best possible *baseline* experience and progressively enhancing it where it makes sense.

I found their work through their [Every Layout](https://every-layout.dev/) project. Every Layout explains how to build common layout components with simple CSS that works *with* the browser instead of against it.

The Grid layout in Every Layout creates an even grid of content where the developer has control over the minimum width of the columns. The clever CSS makes sure each column is of equal width and avoids the common issue with flexbox where a single element on the last row stretches to fill the available space.

Here's the code:

```css
.grid {
    display: grid;
    gap: var(--gutter, 1em);
    grid-template-columns: repeat(
        auto-fill,
        minmax(min(150px, 100%), 1fr)
    );
}
```

And here's the result:

<div class="align-wide resize dashed-border">
<p class="codepen" data-height="300" data-default-tab="result" data-slug-hash="XJrwOpj" data-pen-title="Every Layout Grid" data-editable="true" data-user="mfinell" style="height: 300px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
  <span>See the Pen <a href="https://codepen.io/mfinell/pen/XJrwOpj">
  Every Layout Grid</a> by Markus Finell (<a href="https://codepen.io/mfinell">@mfinell</a>)
  on <a href="https://codepen.io">CodePen</a>.</span>
</p>
<script async src="https://public.codepenassets.com/embed/index.js"></script>
</div>

The columns are at least 150 px wide, unless the available space is less than 150 px, in which case there will be a single, full width column. Beautiful.

The issue I found with this solution was that as the viewport grows, new columns are added as soon as there is available space, and this never stops. If there are 1500 px of available space there will be ten 150 px columns (if there are no gaps).

I have rarely needed more than five or six columns in a desktop layout, but I have used a two column layout on narrow viewports. So I wanted to modify the grid to allow me to set a maximum number of columns. So that when the container fits the maximum number of columns, the columns just keep growing with the container and no more columns are added.

Like so:

<div class="align-wide resize dashed-border">
  <p class="codepen" data-height="300" data-default-tab="result" data-slug-hash="zxOQeJz" data-pen-title="Every Layout Grid" data-editable="true" data-user="mfinell" style="height: 300px; box-sizing: border-box; display: flex; align-items: center; justify-content: center; border: 2px solid; margin: 1em 0; padding: 1em;">
    <span>See the Pen <a href="https://codepen.io/mfinell/pen/zxOQeJz">
    Every Layout Grid</a> by Markus Finell (<a href="https://codepen.io/mfinell">@mfinell</a>)
    on <a href="https://codepen.io">CodePen</a>.</span>
  </p>
  <script async src="https://public.codepenassets.com/embed/index.js"></script>
</div>

The columns are still at least 150px wide, and as the viewport gets narrower they wrap until there's only a single full width column left. But I have set the max number of columns to 4, which causes them to keep growing with the available space when the grid reaches the width of 4 columns. This is useful when using fluid font sizes, and the font size in wide viewports grows beyond what would fit in a narrower column using the original grid solution. My version lets the columns keep growing with the fluid font size.

Here is the CSS for my version of The Grid:

```css
.grid {
  --min-width: 150px;
  --max-columns: 4;
  --w: max(var(--min-width), calc(100% * (1 / (var(--max-columns) + 1))));

  display: grid;
  gap: var(--gutter);
  grid-template-columns: repeat(auto-fill, minmax(min(var(--w), 100%), 1fr));
}
```

<style>
.align-wide.resize {
  width: min(960px + 3em, 100vw);
  max-width: 100vw;
  padding: 1.5em;
  /* border: 2px dashed var(--color--foreground); */
  overflow: hidden;
  resize: horizontal;
  position: relative;
}

.resize::after {
  pointer-events: none;
  content: "↔";
  position: absolute;
  font-size: 1rem;
  height: 1.5em;
  width: 1.5em;
  text-align: center;
  bottom: -0.1em;
  right: -0.1em;
  z-index: 3;
  background-color: var(--color--background);
}
</style>
