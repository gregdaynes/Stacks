#!/bin/bash

# module name
module_name='mod_stacks';

# build dir
build_dir=$PWD;

# code dir
code_dir=../code;

# remove existing tmp dir
rm -rf ../tmp;

# create new tmp dir
mkdir ../tmp
tmp_dir=../tmp;

# copy code contents to tmp
cp -r $code_dir/* $tmp_dir;

# ------------
# Moving Files

# change dir to tmp
cd $tmp_dir;

# move mod_XXX contents into tmp
mv modules/$module_name/* ./;

# remove modules dir
rm -rf .modules;

# move mod_XXX media up into parent media folder
mv media/$module_name/* media/;

# ------------------------
# find/compress javascript
for i in $( find $tmp_dir -type f -name '*.js'); do
	
	# compress with yuicompressor
	# writes over file
	echo 'Compressing '$i
	java -jar $build_dir/yuicompressor-2.4.2.jar -o $i $i;
	
done;

# find less files
for i in $( find $tmp_dir -type f -name '*.less'); do
	# complie less to css with ruby gem
	lessc $i;
done;

# find stylesheet files
for i in $( find $tmp_dir -type f -name '*.css'); do
	# compress with yuicompressor
	# writes over file
	echo 'Compressing '$i
	java -jar $build_dir/yuicompressor-2.4.2.jar -o $i $i;
	
done;

# --------------------------
# smush images (gif png jpg)
echo ''
echo 'Smushing images...'
java -jar $build_dir/smushit.jar -imageDir=$tmp_dir;

# ------------------
# get module version
version='version';
version_num=`grep '<'$version'>' $module_name.xml | tr -d '\t' | sed 's/^<.*>\([^<].*\)<.*>$/\1/'`;

#-----------------
# remove resources
echo ''
echo 'Removing design resources...'
find $tmp_dir -name '*.psd' | xargs rm -rf;
find $tmp_dir -name '*.esproj' | xargs rm -rf;

# remove os x info
echo ''
echo 'Removing resource forks...'
find ./tmp -name '*.DS_Store' | xargs rm -rf;
export COPYFILE_DISABLE=true

# --------
# compress

filename=$module_name'-'$version_num'.zip';
zip -r ../releases/$filename ./*;

# --------------
# remove tmp dir

rm -rf $PWD;