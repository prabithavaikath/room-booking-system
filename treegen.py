#!/usr/bin/env python3
"""
Generate ASCII tree structure for project documentation
Usage: python treegen.py [path] [options]
"""

import os
import sys
import argparse
from pathlib import Path

class TreeGenerator:
    def __init__(self, root_path="."):
        self.root = Path(root_path).resolve()
        self.exclude_dirs = {'.git', '__pycache__', '.idea', '.vscode', 'node_modules', 'vendor', 'dist', 'build'}
        self.exclude_files = {'.DS_Store', '*.pyc', '*.log'}
        self.max_depth = 10
        self.include_hidden = False
        self.dir_only = False
        
    def generate(self, current_path=None, prefix="", is_last=True, depth=0):
        """Recursively generate tree structure"""
        if current_path is None:
            current_path = self.root
            
        if depth > self.max_depth:
            return ""
            
        # Get relative path for display
        if current_path == self.root:
            display_name = current_path.name + "/"
        else:
            display_name = current_path.name
            
        lines = []
        
        # Current directory/file line
        if current_path == self.root:
            lines.append(f"{display_name}")
        else:
            connector = "└── " if is_last else "├── "
            suffix = "/" if current_path.is_dir() else ""
            lines.append(f"{prefix}{connector}{display_name}{suffix}")
            
        # If it's a directory, process contents
        if current_path.is_dir():
            try:
                items = list(current_path.iterdir())
                
                # Filter items
                items = self._filter_items(items)
                
                # Sort: directories first, then files, alphabetically
                items.sort(key=lambda x: (not x.is_dir(), x.name.lower()))
                
                # Update prefix for children
                child_prefix = prefix + ("    " if is_last else "│   ")
                
                # Process each item
                for i, item in enumerate(items):
                    child_is_last = (i == len(items) - 1)
                    lines.append(self.generate(
                        item, 
                        child_prefix, 
                        child_is_last, 
                        depth + 1
                    ))
            except PermissionError:
                lines.append(f"{prefix}    └── [Permission denied]")
                
        return "\n".join(filter(None, lines))
    
    def _filter_items(self, items):
        """Filter out excluded items"""
        filtered = []
        for item in items:
            # Skip hidden files if not included
            if not self.include_hidden and item.name.startswith('.'):
                continue
                
            # Skip excluded directories
            if item.is_dir() and item.name in self.exclude_dirs:
                continue
                
            # Skip excluded file patterns
            if item.is_file():
                skip = False
                for pattern in self.exclude_files:
                    if pattern.startswith('*'):
                        if item.name.endswith(pattern[1:]):
                            skip = True
                            break
                    elif item.name == pattern:
                        skip = True
                        break
                if skip:
                    continue
                    
            # If dir_only mode, skip files
            if self.dir_only and item.is_file():
                continue
                
            filtered.append(item)
            
        return filtered
    
    def update_readme(self, readme_path="README.md", section_start="<!-- TREE_START -->", section_end="<!-- TREE_END -->"):
        """Update tree section in README.md"""
        tree_content = self.generate()
        
        # Create markers with tree
        new_section = f"{section_start}\n```\n{tree_content}\n```\n{section_end}"
        
        try:
            with open(readme_path, 'r') as f:
                content = f.read()
                
            # Replace section
            start_idx = content.find(section_start)
            end_idx = content.find(section_end)
            
            if start_idx != -1 and end_idx != -1:
                # Update existing section
                new_content = (
                    content[:start_idx] + 
                    new_section + 
                    content[end_idx + len(section_end):]
                )
            else:
                # Append to end of file
                new_content = content + "\n\n## Project Structure\n" + new_section
                
            with open(readme_path, 'w') as f:
                f.write(new_content)
                
            print(f"✅ Updated {readme_path}")
            return True
            
        except FileNotFoundError:
            # Create new README
            with open(readme_path, 'w') as f:
                f.write(f"# {self.root.name}\n\n## Project Structure\n{new_section}")
            print(f"✅ Created {readme_path} with structure")
            return True
        except Exception as e:
            print(f"❌ Error updating README: {e}")
            return False
    
    def save_to_file(self, output_file="PROJECT_STRUCTURE.txt"):
        """Save tree to a file"""
        tree_content = self.generate()
        with open(output_file, 'w') as f:
            f.write(tree_content)
        print(f"✅ Saved to {output_file}")
        return True

def main():
    parser = argparse.ArgumentParser(description="Generate ASCII tree structure")
    parser.add_argument("path", nargs="?", default=".", help="Directory path (default: current)")
    parser.add_argument("-o", "--output", help="Output file (default: print to console)")
    parser.add_argument("-r", "--readme", action="store_true", help="Update README.md")
    parser.add_argument("-d", "--dirs-only", action="store_true", help="Show directories only")
    parser.add_argument("-a", "--all", action="store_true", help="Show hidden files")
    parser.add_argument("-e", "--exclude", nargs="+", help="Additional patterns to exclude")
    parser.add_argument("-i", "--include", nargs="+", help="File extensions to include only")
    parser.add_argument("--max-depth", type=int, default=10, help="Maximum depth (default: 10)")
    
    args = parser.parse_args()
    
    # Initialize generator
    generator = TreeGenerator(args.path)
    
    # Apply arguments
    generator.dir_only = args.dirs_only
    generator.include_hidden = args.all
    generator.max_depth = args.max_depth
    
    if args.exclude:
        generator.exclude_dirs.update(args.exclude)
    
    # Generate and output
    if args.readme:
        generator.update_readme()
    elif args.output:
        generator.save_to_file(args.output)
    else:
        # Print to console
        print(generator.generate())
        
        # Show stats
        total_dirs = sum(1 for _ in Path(args.path).rglob('*') if _.is_dir())
        total_files = sum(1 for _ in Path(args.path).rglob('*') if _.is_file())
        print(f"\n📊 Statistics: {total_dirs} directories, {total_files} files")

if __name__ == "__main__":
    main()