# Matomo Simple A/B Testing Plugin

## Description

Free A/B Testing plugin for Matomo.

I created this as an alternative to Google Optimize, which was a free A/B testing tool, but ended end 2024.

Unfortunately, barely any tools are free, let alone affordable.

Want to start with A/B testing? You previously needed a big corporate contract for A/B testing software, making this out of reach for many small entrepreneurs and site owners.

That is why I built Matomo Simple A/B Testing.

1. Insert the custom A/B testing JS script once per domain.
2. Give a name for the experiment.
3. Insert custom CSS for the variant.
4. And/or insert custom JS for the variant.
5. Specify a regex for the page URLs where the experiment should be running (this is not used when injecting the script with Matomo Tag Manager).
6. Specify a Custom Dimension (a Visit Dimension) for the experiment.

## Use Simple A/B Testing with Matomo Tag Manager

1. In your Matomo Tag Manager container, create a new tag - choose "Simple A/B Testing".
2. Set a name for the tag
3. Add description (optional)
4. At "Configure what this tag should do", choose the the experiment you want to add.
5. At "Configure when the tag should do this", choose a trigger (Page view works fine).
6. Click "Create new tag"
7. Publish the change of your container.
8. Test it!

View at the Matomo plugin store:
[Matomo Plugin Page](https://plugins.matomo.org/SimpleABTesting)

Read more at the plugin page:
[Matomo Simple A/B Testing](https://www.nofrillsplugins.com/matomo-simple-ab-testing)

Need more functionality?
[Favorite (Paid) A/B Testing Tools](https://www.nofrillsplugins.com/blog/favorite-ab-testing-tools)

## Important to Know

This is a beta version. Keep this in mind.
