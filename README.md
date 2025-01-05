# Matomo Simple A/B Testing Plugin

## Description

## Adding a new experiment

1. From Dashboard -> Simple A/B Testing -> Create a new experiment.
2. Set a name for the experiment.
3. Insert custom CSS or JS for the variant.
4. Specify a Custom Dimension (a Visit Dimension) for the experiment.

## Activate the experiment

1. In your Matomo Tag Manager container, create a new tag - choose "Simple A/B Testing".
2. Set a name for the tag
3. Add description (optional)
4. At "Configure what this tag should do", choose the the experiment you want to add.
5. At "Configure when the tag should do this", choose a trigger (Pageview works fine).
6. Click "Create new tag"
7. Publish the change of your container.
8. Test it!

## Fork

This is a fork of the original [Simple A/B testing-plugin](https://github.com/nofrillsplugins/matomo-simple-ab-testing). At the moment we can't use the original plugin for some reasons:

* The original author is anonymous and the git commits doesn't have a GitHub user (vagrant) assigned to them. This is not good for a security perspective and goes against the openness we strive for in Digitalist Open Cloud.
* We have contributed back some of the changes we have done, if we can get this in, we will also contribute back the other changes we have added (like adding experiments with Matomo Tag Manager) if the original author starts uses an existing github user for commits and managing the plugin.
