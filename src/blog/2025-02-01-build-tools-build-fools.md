---
title: Build tools, build fools
description: Build tools are great. Until they're not.
---
This site is built with Astro. Until recently I had copied a dev/build setup from Andy Bell that they use at Set Studio. But there's a bit of setup and tweaks to be made to the default Astro setup, and it incorporates tailwind, which I'm not a fan of.

Sure, it's nice to have utility classes generated on the fly and all that. But rather than dealing with all of the build setup and error debugging, I would honestly prefer to just write all of the default, global, and utility CSS manually, setup a ton of custom properties, and reuse that as a base for each project.

So that is what I did for this site. It looks the same, but the build is just the default Astro setup, no plugins or anything. 