---
title: An improved grid from "Every Layout"
---

I am a big fan of [Andy Bell](https://andy-bell.co.uk) and [Heydon Pickering](https://heydonworks.com). They want to build the best possible experiences for everyone. Not by slapping on megabytes of JavaScript libraries and CSS frameworks and building everything in React, but by building the best possible *baseline* experience and progressively enhancing it where it makes sense.

I found their work through their [Every Layout](https://every-layout.dev/) project. Every Layout explains how to build common layout components with simple CSS that works *with* the browser instead of against it.

The Grid layout in Every Layout creates an even grid of content where the developer has control over the minimum width of the columns. The clever CSS makes sure each column is of equal width and avoids the common issue with flexbox where a single element on the last row stretches to fill the available space. 

Here's the code:

```css
.grid {
    display: grid;
    gap: var(--gutter, 1em);
    grid-template-columns: repeat(
        auto-fill, 
        minmax(min(100px, 100%), 1fr)
    );
}
```

And here's the result:

<div class="every-layout-grid example-grid gutter-s align-full">
<div></div>
<div></div>
<div></div>
<div></div>
<div></div>
<div></div>
</div>

The columns are at least 100 px wide, unless the available space is less than 100 px, in which case there will be a single, full width column. Beautiful. 

The issue I found with this solution was that as the viewport grows, new columns are added as soon as there is available space, and this never stops. If there are 1000 px of available space there will be ten 100 px columns (if there are no gaps).

I have rarely needed more than five or six columns in a desktop layout, but I have used a two column layout on narrow viewports. So I wanted to modify the grid to allow me to set a maximum number of columns. So that when the container fits the maximum number of columns, the columns just keep growing with the container and no more columns are added. 

Like so:

<div class="grid gutter-s example-grid align-full" style="--min-width:150px;--max-columns:4">
<div></div>
<div></div>
<div></div>
<div></div>
<div></div>
<div></div>
</div>

The columns are still at least 100 px wide, and as the viewport gets narrower they wrap until there's only a single full width column left. But I have set the max number of columns to 4, which causes them to keep growing with the available space when the grid reaches the width of 4 columns. This is useful when using fluid font sizes, and the font size in wide viewports grows beyond what would fit in a 100 px wide column using the original grid solution. My version lets the columns keep growing with the fluid font size.

Here is the CSS for my version of The Grid:

```css
.grid {
    --min-width: 100px;
    --max-columns: 4;
    --w: max(
        var(--min-width), 
        calc(100% * (1 / (var(--max-columns) + 1)))
    );

    display: grid;
    gap: var(--gutter, 1em);
    grid-template-columns: repeat(
        auto-fill, 
        minmax(min(var(--w), 100%), 1fr)
    );
}
```

<style>
.every-layout-grid {
    display: grid;
    gap: var(--gutter, 1em);
    grid-template-columns: repeat(auto-fill, minmax(min(150px, 100%), 1fr));
}

.example-grid > * {
    min-height: 100px;
    background-color: var(--color-dark);
    color: var(--color-light);
    padding: var(--space-s);
    font-size: clamp(1rem, 0.4783rem + 2.6087vw, 2.25rem);
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>